# Profile Image Setup Guide

## Database Updates

1. **Run the database update script:**
   ```sql
   -- Execute the update_database.sql file in your MySQL database
   -- This adds the profile_image column to the users table
   ```

2. **Or manually add the column:**
   ```sql
   USE alzcare_plus;
   ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) DEFAULT NULL AFTER status;
   ```

## File Structure

The following files have been created/modified:

- `database/schema.sql` - Updated with profile_image column
- `includes/database.php` - Added updateProfileImage method
- `includes/header.php` - Updated to display profile images
- `upload_profile_image.php` - Handles image uploads
- `logout.php` - Handles user logout
- `assets/css/style.css` - Added CSS for profile images
- `assets/images/profiles/` - Directory for uploaded images

## Features

### ✅ **Profile Image Display**
- Profile images appear in the header user dropdown button
- Profile images appear in the dropdown menu
- Profile images appear in the profile modal
- Fallback to default user icon if no image is set

### ✅ **Image Upload**
- Click the camera icon in the profile modal to upload
- Supports JPG, PNG, and GIF formats
- Maximum file size: 5MB
- Automatic file naming with user ID and timestamp
- Real-time update of all profile images on the page

### ✅ **Database Integration**
- Profile image paths stored in database
- Session updated with profile image path
- Proper error handling and validation

## Usage

1. **Login to the system**
2. **Click on your name in the header** (top right)
3. **Click "Profile"** from the dropdown
4. **Click the camera icon** on your profile picture
5. **Select an image file** (JPG, PNG, or GIF, max 5MB)
6. **The image will be uploaded and displayed immediately**

## Default Avatar

To set a default avatar image:
1. Place a default avatar image at `assets/images/default-avatar.png`
2. The image should be approximately 120x120 pixels
3. This will be shown when no profile image is uploaded

## Security Features

- File type validation (only images allowed)
- File size limits (5MB maximum)
- Unique filename generation
- Proper error handling
- Session-based authentication required
- Automatic cleanup of failed uploads

## CSS Classes Added

- `.user-profile-image` - Profile image in header button
- `.dropdown-profile-image` - Profile image in dropdown menu
- Both have circular styling with borders and proper sizing
