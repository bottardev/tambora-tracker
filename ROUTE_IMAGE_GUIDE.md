# Route Image Management Guide

## 🖼️ **Adding Images to Routes**

### How to Upload Route Images

1. **Access Routes Management:**
   - Login to admin panel: `http://your-domain.com/admin`
   - Navigate to **Master Data** → **Routes**

2. **Edit Existing Route:**
   - Click the **Edit** button (pencil icon) on any route
   - You'll see a new **"Route Image"** field

3. **Upload Image:**
   - Click on the image upload area
   - Select an image file (JPG, PNG, WebP)
   - The system supports image editing and cropping
   - Recommended size: **800x600px** or larger
   - Aspect ratios supported: 16:9, 4:3, 1:1

4. **Save Changes:**
   - Click **Save** 
   - Image will appear immediately on the homepage

### Creating New Route with Image

1. **Add New Route:**
   - Go to **Routes** → **Create Route**
   - Fill in route details (Name, Description, Distance)
   - Upload route image in the **"Route Image"** field
   - Add path coordinates if available
   - Save the route

## 🎨 **Image Guidelines**

### Recommended Specifications
- **Format:** JPG, PNG, or WebP
- **Size:** 800x600px or larger (up to 2048x1536px)
- **Aspect Ratio:** 16:9 (landscape) works best
- **File Size:** Under 5MB for best performance
- **Quality:** High quality, clear, well-lit photos

### Best Practices
- **Scenic Views:** Show the route's scenery or landmarks
- **Action Shots:** Hikers on the trail (with permission)
- **Landscape Photos:** Mountain views, forests, or natural features
- **Clear & Bright:** Avoid dark or blurry images
- **No Text Overlay:** Keep images clean without text

### Image Editing Features
The admin panel includes built-in image editing:
- **Crop & Resize:** Adjust image dimensions
- **Aspect Ratio:** Set to 16:9 for consistency
- **Rotate:** Fix orientation if needed
- **Preview:** See how it will look before saving

## 📂 **Where Images are Stored**

- **Storage Location:** `storage/app/public/route-images/`
- **Public Access:** `public/storage/route-images/`
- **URL Pattern:** `http://your-domain.com/storage/route-images/filename.jpg`

## 🔧 **Troubleshooting**

### Image Not Showing?
1. **Check File Upload:** Ensure image uploaded successfully in admin
2. **Storage Link:** Run `php artisan storage:link` if needed
3. **File Permissions:** Check storage directory permissions
4. **Clear Cache:** Clear browser cache and try again

### Upload Failed?
1. **File Size:** Ensure image is under 5MB
2. **File Format:** Use JPG, PNG, or WebP only
3. **Server Space:** Check available disk space
4. **PHP Settings:** Verify upload_max_filesize in PHP config

### Default Images
If no image is uploaded, routes will show a default mountain landscape image from Unsplash.

## 💡 **Tips for Great Route Images**

### Photography Tips
- **Golden Hour:** Shoot during sunrise/sunset for warm lighting
- **Wide Angle:** Capture the scale and beauty of the landscape
- **Include Context:** Show the trail, difficulty, or unique features
- **Weather:** Clear skies usually photograph better
- **Safety First:** Don't risk safety for a photo

### Sourcing Images
- **Take Your Own:** Best option for authenticity
- **Stock Photos:** Use royalty-free mountain/hiking images
- **Attribution:** Credit photographers when required
- **Consistency:** Try to maintain similar style across routes

## 🚀 **Quick Example**

**To add an image to "Tambora Summit Route":**

1. Go to `/admin/routes`
2. Click **Edit** on "Tambora Summit Route"
3. In **"Route Image"** field, click upload area
4. Select your mountain photo (e.g., `tambora-summit.jpg`)
5. Crop to 16:9 aspect ratio using the editor
6. Click **Save**
7. Visit homepage - your image now appears on the route card!

---

## 📋 **Image Management Checklist**

- [ ] Image is high quality and well-lit
- [ ] File size under 5MB
- [ ] Aspect ratio set to 16:9 for consistency  
- [ ] Image represents the route accurately
- [ ] No copyright issues
- [ ] Tested on homepage display
- [ ] Mobile-friendly (looks good on small screens)

Your route images will make the booking experience much more appealing to potential hikers! 🏔️📸