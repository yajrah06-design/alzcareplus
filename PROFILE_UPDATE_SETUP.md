# Profile Update Functionality Setup Guide

## Database Updates Required

To enable the profile update functionality, you need to add the `phone` field to your users table.

### Option 1: Run the Update Script

Execute the following SQL script in your MySQL database:

```sql
-- Run this file: database/update_profile_fields.sql
USE alzcare_plus;

-- Add phone field to users table if it doesn't exist
ALTER TABLE users ADD COLUMN phone VARCHAR(20) DEFAULT NULL AFTER profile_image;

-- Update existing users to have NULL phone (they can update it in their profile)
UPDATE users SET phone = NULL WHERE phone IS NULL;
```

### Option 2: Manual Database Update

If you prefer to run the command manually:

```sql
USE alzcare_plus;
ALTER TABLE users ADD COLUMN phone VARCHAR(20) DEFAULT NULL AFTER profile_image;
```

## New Files Created

The following files have been created for the profile functionality:

- `update_profile.php` - Backend script to handle profile updates
- `get_profile_data.php` - Backend script to retrieve current profile data
- `database/update_profile_fields.sql` - Database update script
- `PROFILE_UPDATE_SETUP.md` - This setup guide

## Modified Files

The following files have been updated:

- `includes/header.php` - Updated profile modal and JavaScript functionality
- `includes/database.php` - Added `updateProfile()` method to Auth class
- `database/schema.sql` - Added phone field to users table
- `index.php` - Added phone to session data during login
- `update_profile.php` - Backend profile update handler

## Features Implemented

### ✅ Profile Modal Enhancements
- **Removed bio field** as requested
- **Added phone number field** with validation
- **Pre-populated fields** with current user data
- **Real-time validation** for email and phone formats

### ✅ Backend Functionality
- **Secure profile updates** with proper validation
- **Email uniqueness check** to prevent conflicts
- **Phone number validation** with flexible format support
- **Session updates** to reflect changes immediately
- **Activity logging** for security tracking

### ✅ Frontend Features
- **Loading states** during save operations
- **Error handling** with user-friendly messages
- **Success notifications** with visual feedback
- **Form validation** before submission
- **Dynamic header updates** to show new name immediately

## How to Test

1. **Run the database update** using one of the options above
2. **Login to your admin account**
3. **Click on your name** in the header to access the dropdown
4. **Select "Profile"** to open the profile modal
5. **Update your information**:
   - Change your full name
   - Update your phone number
   - Modify your email (if desired)
6. **Click "Save Changes"** to update your profile
7. **Verify the changes** are reflected in the header and database

## Security Features

- **Input validation** on both frontend and backend
- **SQL injection protection** using prepared statements
- **Session management** for secure user identification
- **Email uniqueness** validation to prevent conflicts
- **Phone number format** validation with flexible patterns
- **Activity logging** for audit trails

## Error Handling

The system handles various error scenarios:

- **Missing required fields** (name, email)
- **Invalid email format**
- **Invalid phone number format**
- **Email already taken by another user**
- **Database connection errors**
- **Session timeout/unauthorized access**

All errors are displayed to the user with clear, actionable messages.

