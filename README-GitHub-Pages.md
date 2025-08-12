# AlzcarePlus - GitHub Pages Setup Guide

## 🚀 Quick Setup for GitHub Pages

This guide will help you deploy your AlzcarePlus healthcare management system to GitHub Pages for free hosting.

## 📋 Prerequisites

1. **GitHub Account** - Create one at [github.com](https://github.com)
2. **Git** - Install from [git-scm.com](https://git-scm.com)

## 🔧 Step-by-Step Setup

### Step 1: Create Repository
1. Go to [github.com](https://github.com)
2. Click "+" → "New repository"
3. Name: `alzcareplus`
4. Make it **Public** (required for free hosting)
5. Click "Create repository"

### Step 2: Upload Files
1. **Option A: Upload via GitHub Web Interface**
   - Click "uploading an existing file"
   - Drag and drop all your files
   - Commit changes

2. **Option B: Use Git Commands**
   ```bash
   git clone https://github.com/YOUR_USERNAME/alzcareplus.git
   cd alzcareplus
   # Copy your files here
   git add .
   git commit -m "Initial commit"
   git push origin main
   ```

### Step 3: Enable GitHub Pages
1. Go to repository **Settings**
2. Scroll to **Pages** section
3. **Source**: Select "Deploy from a branch"
4. **Branch**: Select "main" and "/ (root)"
5. Click **Save**

### Step 4: Access Your Website
- **URL**: `https://YOUR_USERNAME.github.io/alzcareplus`
- **Wait**: 5-10 minutes for first deployment

## 📁 File Structure for GitHub Pages

```
alzcareplus/
├── index.html          # Main landing page
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── README.md
└── README-GitHub-Pages.md
```

## ⚠️ Important Notes

- **PHP files won't work** - GitHub Pages only supports static files
- **Database functionality** - Not available in static hosting
- **This is a demo** - Shows the UI and features
- **Full app** - Requires PHP hosting for complete functionality

## 🌐 Alternative Hosting Options

For full PHP functionality, consider:
- **Shared Hosting**: Hostinger, Bluehost ($3-10/month)
- **VPS**: DigitalOcean, Linode ($5-20/month)
- **Cloud**: AWS, Google Cloud (pay-as-you-use)

## 🎯 Next Steps

1. **Deploy to GitHub Pages** (this guide)
2. **Test the demo** with users
3. **Get feedback** on design and features
4. **Upgrade to PHP hosting** when ready for full functionality
5. **Buy custom domain** (optional, $10-15/year)

## 📞 Support

- **GitHub Issues**: Create issue in repository
- **Documentation**: Check README.md for app details
- **Features**: See index.html for demo

---

**AlzcarePlus** - Making healthcare management easier for Alzheimer's patients and caregivers.
