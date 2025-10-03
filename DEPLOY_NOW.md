# 🚀 **DEPLOY TO RAILWAY NOW!**

## ⚡ **Step 1: Create GitHub Repository**

1. **Go to**: https://github.com/new
2. **Repository name**: `On-The-Night-CMS`
3. **Make Public**: ✅ (required for free Railway)
4. **Don't** initialize with README (we have code ready)
5. **Click**: "Create repository"

## ⚡ **Step 2: Push Code to GitHub**

**Run these commands in your terminal:**

```bash
# Replace YOUR_USERNAME with your actual GitHub username
git remote add origin https://github.com/YOUR_USERNAME/On-The-Night-CMS.git
git branch -M main  
git push -u origin main
```

## ⚡ **Step 3: Deploy to Railway**

1. **Go to**: https://railway.app
2. **Sign up** with GitHub
3. **Click**: "New Project"
4. **Select**: "Deploy from GitHub repo"
5. **Choose**: "On-The-Night-CMS"
6. **Click**: "Deploy"

## ⚡ **Step 4: Configure Environment (If Needed)**

Railway will auto-detect Laravel and set up:
- ✅ Composer dependencies
- ✅ Database migrations  
- ✅ App key generation
- ✅ Database seeding

**If asked for environment variables, add:**
```
APP_URL=https://your-app-name.railway.app
```

## ⚡ **Step 5: Get Your Public URLs**

After deployment (2-3 minutes):
- **API**: `https://your-app-name.railway.app/api/v1`
- **Admin**: `https://your-app-name.railway.app/admin`

**Admin Login Credentials:**
- **Email**: `admin@onthenight.com`
- **Password**: `password`

## 🎉 **You're Live!**

**Total time**: ~5 minutes
**Result**: Permanent public API for Figma Make

---

## 🔗 **Quick Links:**

- **GitHub**: https://github.com/new
- **Railway**: https://railway.app
- **Your API**: Will be `https://your-app.railway.app/api/v1`
