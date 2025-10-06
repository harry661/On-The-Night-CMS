# Figma Make Status Cards Troubleshooting 🎯

## 🚨 **PROBLEM**: Status Cards Showing No Venues

**Backend Status:** ✅ **WORKING PERFECTLY**
- API is live and responding: `https://plummier-unhumiliating-camelia.ngrok-free.dev/api/v1/data`
- All venue statuses are correct: `featured`, `sponsored`, `new`
- Data is complete and properly formatted

---

## 🔧 **SOLUTION STEPS FOR FIGMA MAKE:**

### **Step 1: Verify Base URL Configuration**

❌ **Wrong URL Format:**
```
https://plummier-unhumiliating-camelia.ngrok-free.dev/
```

✅ **Correct Base URL:**
```
https://plummier-unhumiliating-camelia.ngrok-free.dev/api/v1/
```

### **Step 2: Use Single Data Endpoint**

**For ALL status cards, use ONE data source:**

**Endpoint:** `/data`  
**Full URL:** `https://plummier-unhumiliating-camelia.ngrok-free.dev/api/v1/data`

### **Step 3: Data Structure Understanding**

The API returns:
```json
{
  "success": true,
  "data": {
    "venues": [
      {
        "id": 2,
        "name": "Electrik Warehouse",
        "status": "featured",
        "images": [...],
        "location": {...}
      }
    ]
  }
}
```

**Key Points:**
- Venues are nested under `data.venues`
- Each venue has a `status` поле
- Available statuses: `featured`, `sponsored`, `new`, `none`

### **Step 4: Card Filtering Configuration**

For each card type, filter from the SAME data source:

#### **Featured Cards** 🟡
- **Data Source:** `/data` endpoint
- **Filter:** `venues.status == "featured"`
- **Count:** 3 venues (Electrik Warehouse, Heebie Jeebies, The Ship & Mitre)

#### **Sponsored Cards** 🔵
- **Data Source:** `/data` endpoint  
- **Filter:** `venues.status == "sponsored"`
- **Count:** 2 venues (Alma de Cuba, Boom Battle Bar)

#### **New Cards** 🟢
- **Data Source:** `/data` endpoint
- **Filter:** `venues.status == "new"`
- **Count:** 2 venues (Level, The Jacaranda)

---

## 🧪 **Testing Commands**

**Test Each Status:**

```bash
# Featured venues count
curl "https://plummier-unhumiliating-camelia.ngrok-free.dev/api/v1/data" | grep '"status":"featured"' | wc -l
# Expected: 3

# Sponsored venues count  
curl "https://plummier-unhumiliating-camelia.ngrok-free.dev/api/v1/data" | grep '"status":"sponsored"' | wc -l
# Expected: 2

# New venues count
curl "https://plummier-unhumiliating-camelia.ngrok-free.dev/api/v1/data" | grep '"status":"new"' | wc -l
# Expected: 2
```

---

## 🚨 **COMMON FIGMA MAKE ISSUES**

### **Issue 1: Wrong JSON Path**
❌ **Wrong:** `venues[0].status`
✅ **Correct:** `data.venues[].status`

### **Issue 2: Multiple Data Sources**
❌ **Wrong:** Separate endpoints for each status
✅ **Correct:** One endpoint with filters

### **Issue 3: Case Sensitivity**
❌ **Wrong:** `"Featured"` or `"FEATURED"`
✅ **Correct:** `"featured"` (lowercase)

### **Issue 4: Caching**
- Clear Figma Make cache/reload the project
- Test endpoint directly in browser first

### **Issue 5: Array vs Object**
❌ **Wrong:** Treating venue as single object
✅ **Correct:** Filtering array of venues

---

## 💯 **Expected Results**

When configured correctly, each card should show:

| Card Type | Venue Count | Venue Names |
|-----------|-------------|-------------|
| **Featured** | 3 | Electrik Warehouse, Heebie Jeebies, The Ship & Mitre |
| **Sponsored** | 2 | Alma de Cuba, Boom Battle Bar |
| **New** | 2 | Level, The Jacaranda |

---

## 🎯 **Quick Fix Checklist**

- [ ] Base URL includes `/api/v1/` prefix
- [ ] Using single `/data` endpoint for all cards
- [ ] Filtering `data.venues[]` array correctly
- [ ] Status values in lowercase: `featured`, `sponsored`, `new`
- [ ] Cache cleared in Figma Make
- [ ] Test URLs in browser first

---

**🚀 Backend is 100% ready - the issue is in Figma Make's data configuration!**
