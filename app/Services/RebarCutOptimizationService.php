<?php

namespace App\Services;

/**
 * Rebar Cut Optimization Service
 *
 * Implements a column-generation (greedy ILP-like) algorithm to find
 * the minimum number of 12m standard rebar bars needed to fulfill
 * a list of required cut lengths.
 *
 * Standard bar length : 12.0m (configurable)
 * Kerf/saw waste      : 0.02m per cut (configurable)
 *
 * Algorithm (per diameter):
 *  1. Collect all (length, qty) requirements
 *  2. Generate all valid cutting patterns that fit within one bar
 *     (using bounded knapsack enumeration, capped to avoid explosion)
 *  3. Greedily pick the pattern that satisfies the most remaining demand
 *     (column generation heuristic), assign as many bars of that
 *     pattern as needed, then repeat until all demand is met
 *  4. Return patterns used, bars per pattern, waste per bar, totals
 */
class RebarCutOptimizationService
{
    /** Standard rebar bar length in metres */
    public float $barLength = 12.0;

    /** Kerf (saw cut) waste per cut in metres */
    public float $kerf = 0.02;

    /**
     * Run cut optimization for a given diameter.
     *
     * @param  array<int, array{length: float, qty: int, label: string}>  $requirements
     *         Each entry: ['length' => 4.5, 'qty' => 10, 'label' => 'Column C1']
     *
     * @return array{
     *   patterns: array<int, array{
     *     cuts: array<int, array{length: float, qty: int, label: string}>,
     *     bars_used: int,
     *     waste: float,
     *     utilization: float
     *   }>,
     *   total_bars: int,
     *   total_waste: float,
     *   total_length_used: float,
     *   requirements: array
     * }
     */
    public function optimize(array $requirements): array
    {
        // Filter out zero-qty or zero-length entries
        $requirements = array_values(array_filter($requirements, fn($r) => $r['qty'] > 0 && $r['length'] > 0));

        if (empty($requirements)) {
            return $this->emptyResult();
        }

        // Validate all lengths fit in a single bar
        foreach ($requirements as $r) {
            if ($r['length'] > $this->barLength) {
                throw new \InvalidArgumentException(
                    "Required length {$r['length']}m exceeds bar length {$this->barLength}m"
                );
            }
        }

        // Generate all valid cutting patterns
        $patterns = $this->generatePatterns($requirements);

        // Greedy column generation: pick best pattern each round
        $remaining = array_column($requirements, 'qty', 'length');
        $usedPatterns = [];

        $maxIterations = 2000;
        $iteration = 0;

        while ($this->hasRemainingDemand($remaining) && $iteration < $maxIterations) {
            $iteration++;

            $bestPattern = null;
            $bestScore   = -1;

            foreach ($patterns as $pattern) {
                // Check pattern can contribute to remaining demand
                $contributes = false;
                foreach ($pattern['cuts'] as $cut) {
                    if (($remaining[$cut['length']] ?? 0) > 0) {
                        $contributes = true;
                        break;
                    }
                }
                if (!$contributes) continue;

                // Score = total length consumed × how much it reduces demand
                $score = 0;
                foreach ($pattern['cuts'] as $cut) {
                    $need = $remaining[$cut['length']] ?? 0;
                    $fulfil = min($cut['qty'], $need);
                    $score += $fulfil * $cut['length'];
                }

                if ($score > $bestScore) {
                    $bestScore   = $score;
                    $bestPattern = $pattern;
                }
            }

            if ($bestPattern === null) break;

            // How many bars of this pattern do we need?
            $barsNeeded = PHP_INT_MAX;
            foreach ($bestPattern['cuts'] as $cut) {
                $need = $remaining[$cut['length']] ?? 0;
                if ($need > 0 && $cut['qty'] > 0) {
                    $barsNeeded = min($barsNeeded, (int) ceil($need / $cut['qty']));
                }
            }
            if ($barsNeeded === PHP_INT_MAX) $barsNeeded = 1;

            // Deduct from remaining demand
            foreach ($bestPattern['cuts'] as $cut) {
                if (isset($remaining[$cut['length']])) {
                    $remaining[$cut['length']] = max(0, $remaining[$cut['length']] - ($cut['qty'] * $barsNeeded));
                }
            }

            // Accumulate used patterns
            $key = $this->patternKey($bestPattern);
            if (isset($usedPatterns[$key])) {
                $usedPatterns[$key]['bars_used'] += $barsNeeded;
            } else {
                $usedPatterns[$key] = array_merge($bestPattern, ['bars_used' => $barsNeeded]);
            }
        }

        // Handle any leftover demand (shouldn't happen but safety net)
        foreach ($remaining as $length => $qty) {
            if ($qty > 0) {
                // One bar per remaining piece (worst case)
                $piecesPerBar = (int) floor(($this->barLength + $this->kerf) / ($length + $this->kerf));
                $piecesPerBar = max(1, $piecesPerBar);
                $barsNeeded   = (int) ceil($qty / $piecesPerBar);
                $used         = min($piecesPerBar, $qty) * $length + max(0, $piecesPerBar - 1) * $this->kerf;
                $waste        = $this->barLength - $used;

                $key = "fallback_{$length}_{$piecesPerBar}";
                $usedPatterns[$key] = [
                    'cuts'      => [['length' => $length, 'qty' => $piecesPerBar, 'label' => "Ø{$length}m"]],
                    'bars_used' => $barsNeeded,
                    'waste'     => round($waste, 4),
                    'utilization' => round(($used / $this->barLength) * 100, 1),
                ];
            }
        }

        // Build final result
        $totalBars      = 0;
        $totalWaste     = 0.0;
        $totalLenUsed   = 0.0;
        $patternList    = [];

        foreach ($usedPatterns as $p) {
            $waste       = $p['waste'];
            $lenUsed     = $this->barLength - $waste;
            $totalBars  += $p['bars_used'];
            $totalWaste += $waste * $p['bars_used'];
            $totalLenUsed += $lenUsed * $p['bars_used'];

            $patternList[] = [
                'cuts'        => $p['cuts'],
                'bars_used'   => $p['bars_used'],
                'waste'       => round($waste, 4),
                'utilization' => round(($lenUsed / $this->barLength) * 100, 1),
            ];
        }

        // Sort by pattern waste ascending (most efficient first)
        usort($patternList, fn($a, $b) => $a['waste'] <=> $b['waste']);

        return [
            'patterns'          => $patternList,
            'total_bars'        => $totalBars,
            'total_waste'       => round($totalWaste, 3),
            'total_length_used' => round($totalLenUsed, 3),
            'requirements'      => $requirements,
        ];
    }

    /**
     * Run optimization for all diameters present in takeoff items.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $sections  (takeoff->sections->items)
     * @return array<int, array{dia: int, result: array, weight_per_meter: float, total_weight_kg: float}>
     */
    public function optimizeFromSections($sections): array
    {
        $unitWeights = [
            8  => 0.395, 10 => 0.617, 12 => 0.889,
            14 => 1.210, 16 => 1.580, 20 => 2.469,
            24 => 3.550, 32 => 6.313,
        ];

        // Group requirements by diameter
        $byDia = [];
        foreach ($sections as $section) {
            foreach ($section->items as $item) {
                if ($item->is_header) continue;

                $dia       = (int) ($item->calculation_data['bar_dia'] ?? 0);
                $barLength = (float) ($item->calculation_data['bar_length'] ?? 0);
                $noOfBar   = (int) ($item->calculation_data['no_of_bar'] ?? 0);
                $noOfMember = (int) ($item->count ?? 1);

                if ($dia <= 0 || $barLength <= 0 || $noOfBar <= 0) continue;

                $totalBars = $noOfBar * $noOfMember;

                if (!isset($byDia[$dia])) $byDia[$dia] = [];

                $byDia[$dia][] = [
                    'length' => round($barLength, 4),
                    'qty'    => $totalBars,
                    'label'  => $item->element ?? "Ø{$dia}",
                ];
            }
        }

        $results = [];
        foreach ($byDia as $dia => $reqs) {
            // Merge duplicate lengths
            $merged = [];
            foreach ($reqs as $r) {
                $key = (string) $r['length'];
                if (isset($merged[$key])) {
                    $merged[$key]['qty'] += $r['qty'];
                    $merged[$key]['label'] .= ', ' . $r['label'];
                } else {
                    $merged[$key] = $r;
                }
            }
            $merged = array_values($merged);

            try {
                $result = $this->optimize($merged);
            } catch (\Exception $e) {
                $result = $this->emptyResult();
                $result['error'] = $e->getMessage();
            }

            $wpm = $unitWeights[$dia] ?? 0;
            $totalWeightKg = round($result['total_length_used'] * $wpm, 2);

            $results[] = [
                'dia'              => $dia,
                'result'           => $result,
                'weight_per_meter' => $wpm,
                'total_weight_kg'  => $totalWeightKg,
            ];
        }

        // Sort by diameter
        usort($results, fn($a, $b) => $a['dia'] <=> $b['dia']);

        return $results;
    }

    /**
     * Run optimization separated by each section.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $sections
     * @return array<int, array{section_id: int, section_name: string, results: array}>
     */
    /**
     * Run optimization separated by each section, carrying over an offcut pool
     * so wastage/offcuts from previous cuts are checked and reused FIRST before
     * allocating any new 12m bars.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $sections
     * @return array<int, array{section_id: int, section_name: string, results: array}>
     */
    public function optimizeBySections($sections): array
    {
        $unitWeights = [
            8  => 0.395, 10 => 0.617, 12 => 0.889,
            14 => 1.210, 16 => 1.580, 20 => 2.469,
            24 => 3.550, 32 => 6.313,
        ];

        // Maintain global offcut pool per diameter across sections
        // Format: [dia => [ ['length' => float, 'qty' => int] ]]
        $offcutPool = [];

        $sectionResults = [];
        foreach ($sections as $section) {
            $sectionName = $section->name ?: 'General Section';

            // Collect items in this section grouped by diameter
            $byDia = [];
            foreach ($section->items as $item) {
                if ($item->is_header) continue;

                $dia        = (int) ($item->calculation_data['bar_dia'] ?? 0);
                $barLength  = (float) ($item->calculation_data['bar_length'] ?? 0);
                $noOfBar    = (int) ($item->calculation_data['no_of_bar'] ?? 0);
                $noOfMember = (int) ($item->count ?? 1);

                if ($dia <= 0 || $barLength <= 0 || $noOfBar <= 0) continue;

                $totalBars = $noOfBar * $noOfMember;
                if (!isset($byDia[$dia])) $byDia[$dia] = [];

                $byDia[$dia][] = [
                    'length' => round($barLength, 4),
                    'qty'    => $totalBars,
                    'label'  => $item->element ?? "Ø{$dia}",
                ];
            }

            if (empty($byDia)) continue;

            $secDiaResults = [];
            foreach ($byDia as $dia => $reqs) {
                // Merge duplicate lengths
                $merged = [];
                foreach ($reqs as $r) {
                    $key = (string) $r['length'];
                    if (isset($merged[$key])) {
                        $merged[$key]['qty'] += $r['qty'];
                    } else {
                        $merged[$key] = $r;
                    }
                }
                $merged = array_values($merged);

                // Sort requirements by length descending (longest first)
                usort($merged, fn($a, $b) => $b['length'] <=> $a['length']);

                if (!isset($offcutPool[$dia])) {
                    $offcutPool[$dia] = [];
                }

                // ── STEP 1: Check Offcut Pool FIRST for available wastage ────
                $offcutFulfilled = [];
                foreach ($merged as &$req) {
                    if ($req['qty'] <= 0) continue;

                    foreach ($offcutPool[$dia] as &$poolItem) {
                        if ($poolItem['qty'] <= 0) continue;

                        // Check if pool offcut length fits the required piece length
                        if ($poolItem['length'] >= $req['length'] - 0.0001) {
                            $take = min($req['qty'], $poolItem['qty']);

                            $req['qty']       -= $take;
                            $poolItem['qty']  -= $take;

                            $remLen = round($poolItem['length'] - $req['length'] - $this->kerf, 4);

                            $key = (string)$req['length'];
                            if (!isset($offcutFulfilled[$key])) {
                                $offcutFulfilled[$key] = [
                                    'length'     => $req['length'],
                                    'qty'        => 0,
                                    'from_offcut_length' => $poolItem['length'],
                                ];
                            }
                            $offcutFulfilled[$key]['qty'] += $take;

                            // If leftover piece is still usable (>= 0.3m), return to pool
                            if ($remLen >= 0.3) {
                                $offcutPool[$dia][] = [
                                    'length' => $remLen,
                                    'qty'    => $take,
                                ];
                            }

                            if ($req['qty'] <= 0) break;
                        }
                    }
                }
                unset($req);

                // ── STEP 2: Only for unfulfilled requirements, open NEW 12m bars! ────
                $remainingReqs = array_values(array_filter($merged, fn($r) => $r['qty'] > 0));

                $optResult = !empty($remainingReqs)
                    ? $this->optimize($remainingReqs)
                    : $this->emptyResult();

                // Add new offcuts from this optimization's patterns to offcutPool
                foreach ($optResult['patterns'] as $p) {
                    if ($p['waste'] >= 0.3 && $p['bars_used'] > 0) {
                        $offcutPool[$dia][] = [
                            'length' => $p['waste'],
                            'qty'    => $p['bars_used'],
                        ];
                    }
                }

                $wpm = $unitWeights[$dia] ?? 0;
                $totalWeightKg = round($optResult['total_length_used'] * $wpm, 2);

                $secDiaResults[] = [
                    'dia'                 => $dia,
                    'result'              => $optResult,
                    'weight_per_meter'    => $wpm,
                    'total_weight_kg'     => $totalWeightKg,
                    'offcut_fulfilled'   => array_values($offcutFulfilled),
                ];
            }

            $sectionResults[] = [
                'section_id'   => $section->id,
                'section_name' => $sectionName,
                'results'      => $secDiaResults,
            ];
        }

        return $sectionResults;
    }

    // ─────────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────────

    /**
     * Generate all valid cutting patterns for a set of requirements.
     * A pattern is one way to cut a single 12m bar.
     * Limited to prevent combinatorial explosion.
     */
    private function generatePatterns(array $requirements): array
    {
        $patterns  = [];
        $lengths   = array_column($requirements, 'length');
        $maxQtys   = array_column($requirements, 'qty');

        // Cap max pieces per type to avoid explosion (practical limit)
        $cappedMaxQtys = array_map(fn($q) => min($q, 20), $maxQtys);

        $this->enumeratePatterns(
            $lengths, $cappedMaxQtys, 0, [], $this->barLength, $patterns
        );

        return $patterns;
    }

    private function enumeratePatterns(
        array $lengths,
        array $maxQtys,
        int   $idx,
        array $current,
        float $remaining,
        array &$patterns,
        int   $maxPatterns = 5000
    ): void {
        if (count($patterns) >= $maxPatterns) return;

        // Record non-empty patterns
        if (!empty($current)) {
            $cuts  = [];
            $used  = 0;
            foreach ($current as $i => $qty) {
                if ($qty > 0) {
                    $cuts[] = ['length' => $lengths[$i], 'qty' => $qty, 'label' => ''];
                    $used  += $lengths[$i] * $qty + $this->kerf * ($qty - 1);
                }
            }
            // Subtract first kerf (first cut doesn't need it)
            if (!empty($cuts)) $used += $this->kerf; // final cut to remainder

            $waste = $this->barLength - ($used - $this->kerf);
            if ($waste < 0) $waste = 0;

            // Recalculate precisely
            $preciseUsed = 0;
            foreach ($current as $i => $qty) {
                if ($qty > 0) {
                    $preciseUsed += $lengths[$i] * $qty;
                    $preciseUsed += $this->kerf * $qty; // kerf per piece
                }
            }
            $preciseWaste = $this->barLength - $preciseUsed;
            if ($preciseWaste < -0.001) {
                // Over-filled — skip this pattern
                return;
            }
            $preciseWaste = max(0, $preciseWaste);

            $patterns[] = [
                'cuts'        => $cuts,
                'waste'       => round($preciseWaste, 4),
                'utilization' => round((($this->barLength - $preciseWaste) / $this->barLength) * 100, 1),
            ];
        }

        if ($idx >= count($lengths)) return;

        $len    = $lengths[$idx];
        $maxQty = $maxQtys[$idx];

        // Try 0..$maxQty pieces of this length
        for ($q = 0; $q <= $maxQty; $q++) {
            $cost = ($len + $this->kerf) * $q;
            if ($cost > $remaining + 0.001) break;

            $newCurrent = $current;
            $newCurrent[$idx] = $q;

            $this->enumeratePatterns(
                $lengths, $maxQtys, $idx + 1,
                $newCurrent, $remaining - $cost,
                $patterns, $maxPatterns
            );
        }
    }

    private function hasRemainingDemand(array $remaining): bool
    {
        foreach ($remaining as $qty) {
            if ($qty > 0) return true;
        }
        return false;
    }

    private function patternKey(array $pattern): string
    {
        $parts = [];
        foreach ($pattern['cuts'] as $cut) {
            $parts[] = "{$cut['length']}x{$cut['qty']}";
        }
        sort($parts);
        return implode('|', $parts);
    }

    private function emptyResult(): array
    {
        return [
            'patterns'          => [],
            'total_bars'        => 0,
            'total_waste'       => 0.0,
            'total_length_used' => 0.0,
            'requirements'      => [],
        ];
    }
}
