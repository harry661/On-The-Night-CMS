# Figma Make Integration Guide for On the Night CMS

## 🎯 **API Base URL**
```
https://plummier-unhumiliating-camelia.ngrok-free.dev
```

## 📊 **Available Endpoints**

### **1. Venues**
- **All Venues**: `GET /api/v1/venues`
- **Single Venue**: `GET /api/v1/venues/{id}`
- **Featured Venues**: `GET /api/v1/venues?status=featured`
- **Sponsored Venues**: `GET /api/v1/venues?status=sponsored`
- **New Venues**: `GET /api/v1/venues?status=new`

### **2. Other Resources**
- **Deals**: `GET /api/v1/deals`
- **Events**: `GET /api/v1/events`
- **Reviews**: `GET /api/v1/reviews`
- **Locations**: `GET /api/v1/locations`
- **Drink Types**: `GET /api/v1/drink-types`
- **Music Genres**: `GET /api/v1/music-genres`

## 🖼️ **Venue Image Integration**

### **Primary Image Field**
For venue cards, use the `image` field from each venue object:

```json
{
  "id": 2,
  "name": "Electrik Warehouse",
  "image": "https://plummier-unhumiliating-camelia.ngrok-free.dev/cors-image/venues/01K6WW6JFSX2VDVN4GJNHHWKVJ.jpg",
  // ... other fields
}
```

### **Image URL Structure**
- **Real Images**: `https://plummier-unhumiliating-camelia.ngrok-free.dev/cors-image/{path}`
- **Placeholder Images**: `https://plummier-unhumiliating-camelia.ngrok-free.dev/images/venue-placeholder.jpg`

### **CORS Support**
All image URLs include proper CORS headers:
- `Access-Control-Allow-Origin: *`
- `Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS`
- `Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With`

## 🎨 **Venue Card Implementation**

### **For Figma Make Venue Cards:**

1. **Image Source**: Use `venue.image` field
2. **Fallback**: If `venue.image` is null/empty, use placeholder
3. **Image Sizing**: Images are optimized to 1920x1080 (16:9 aspect ratio)

### **Example Venue Card Data Structure:**
```json
{
  "id": 2,
  "name": "Electrik Warehouse",
  "description": "Liverpool's premier electronic music venue...",
  "address": "Wood Street",
  "city": "Liverpool",
  "image": "https://plummier-unhumiliating-camelia.ngrok-free.dev/cors-image/venues/01K6WW6JFSX2VDVN4GJNHHWKVJ.jpg",
  "status": "featured",
  "venue_type": {
    "name": "Leisure",
    "color": "#EF4444"
  },
  "opening_hours": {
    "Wednesday": "10:00 PM - 3:00 AM",
    "Thursday": "10:00 PM - 3:00 AM",
    "Friday": "10:00 PM - 4:00 AM",
    "Saturday": "10:00 PM - 4:00 AM",
    "Sunday": "10:00 PM - 2:00 AM"
  }
}
```

## 🔍 **Status-Based Filtering**

### **Featured Venues**
```bash
GET /api/v1/venues?status=featured
```

### **Sponsored Venues**
```bash
GET /api/v1/venues?status=sponsored
```

### **New Venues**
```bash
GET /api/v1/venues?status=new
```

## 📱 **Testing Image URLs**

### **Test Individual Images:**
```bash
curl -I "https://plummier-unhumiliating-camelia.ngrok-free.dev/cors-image/venues/01K6WW6JFSX2VDVN4GJNHHWKVJ.jpg"
```

### **Expected Response:**
```
HTTP/2 200
content-type: image/jpeg
access-control-allow-origin: *
```

## 🚀 **Quick Start for Figma Make**

1. **Set Base URL**: `https://plummier-unhumiliating-camelia.ngrok-free.dev`
2. **Use Venue Endpoint**: `/api/v1/venues`
3. **For Images**: Use `venue.image` field in your cards
4. **Filter by Status**: Add `?status=featured` for featured venues

## ✅ **Current Status**

- ✅ **API Endpoints**: All working
- ✅ **Image URLs**: Properly formatted with CORS
- ✅ **Image Accessibility**: All images load correctly
- ✅ **Placeholder Images**: Available for venues without images
- ✅ **ngrok Tunnel**: Active and stable

## 🔧 **Troubleshooting**

### **If Images Don't Load:**
1. Check if ngrok tunnel is active
2. Verify image URL format
3. Test image URL directly in browser
4. Check CORS headers in browser dev tools

### **If API Returns 404:**
1. Verify ngrok tunnel is running
2. Check if Laravel server is running on port 8000
3. Test with curl: `curl https://plummier-unhumiliating-camelia.ngrok-free.dev/api/v1/venues`

---

**Last Updated**: October 6, 2025
**ngrok URL**: https://plummier-unhumiliating-camelia.ngrok-free.dev
