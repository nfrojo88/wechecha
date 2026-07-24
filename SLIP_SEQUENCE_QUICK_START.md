# Slip Sequence Manager - Quick Start Guide

## 🚀 30-Second Setup

### 1. Run Migration (On Server)
```bash
php artisan migrate --force
```

### 2. Access Dashboard
- Login to ERP
- Go to: **Store Manager → Slip Sequences**

### 3. Create First Sequence
- Click **"Configure New Sequence"**
- Fill in:
  - Store: Main Warehouse
  - Type: Receiving (GRN)
  - Label: Receiving (GRN)
  - Prefix: REC (or leave blank)
  - Book Start: 2100
  - Book End: 2150
- Click **"Save Configuration"**

### 4. Test It
- Go to: **Store Manager → Create Slip**
- Leave "Slip No" BLANK
- Fill other details
- Submit
- **Result**: Slip gets REC02100 automatically ✅

---

## 🎯 Common Tasks

### View All Sequences
Store Manager → Slip Sequences → (see dashboard)

### Create New Book Sequence
Slip Sequences → Configure New Sequence → Fill form → Save

### Pause Sequence (Next Book)
Slip Sequences → Find sequence → Click ⏸️ button

### Resume Using Sequence
Slip Sequences → Find sequence → Click ▶️ button

### Edit Sequence Details
Slip Sequences → Find sequence → Click ✏️ button → Save

---

## 📝 Slip Number Examples

### Format: Prefix + 5-digit number

**With Prefix "REC":**
- REC02100
- REC02101
- REC02102

**Without Prefix (numeric):**
- 02100
- 02101
- 02102

**Book Full Example:**
- Starts: 2100
- Ends: 2150
- After creating 51 slips → Status = "Full"

---

## 🔢 Slip Type Reference

| Type | Abbreviation | Use | Example |
|------|--------------|-----|---------|
| Receiving | GRN | Supplier deliveries | "REC2100" |
| Outgoing | SIN | Transfer to sites | "OUT1501" |

---

## ❓ Quick Answers

**Q: Slip number not auto-generating?**
A: Make sure sequence status is "active" for that store + type

**Q: Book says "Full"?**
A: You've used all slips in that book range. Create new sequence for next book.

**Q: Want to override auto-number?**
A: Manually enter slip number in "Slip No" field (optional)

**Q: How many slips in book 2100-2150?**
A: 51 total (2150 - 2100 + 1)

---

## 📊 Dashboard Info

**Next Slip**: The number that will be assigned to next slip created
**Progress**: Shows % of book used and remaining slips
**Status**: Active (in use) / Inactive (paused) / Full (all used)

---

## 🔗 URLs

- Dashboard: `/store-manager/slip-sequences`
- Create: `/store-manager/slip-sequences/create`
- Edit: `/store-manager/slip-sequences/{id}/edit`
- API: `/store-manager/api/slip-sequences/{storeId}/{slipType}`

---

**For detailed info, see SLIP_SEQUENCE_SETUP.md**
