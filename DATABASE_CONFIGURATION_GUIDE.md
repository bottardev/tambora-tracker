# Database-Based Booking Configuration Guide

## Overview
The booking system now uses database-based configuration that you can easily manage through your admin panel. No more editing files or environment variables!

## ✅ **Key Benefits**
- **Easy Management**: Change settings directly in the admin panel
- **Real-time Updates**: Changes take effect immediately
- **User-friendly Interface**: Visual forms with validation
- **Organized Settings**: Grouped by category (Pricing, Payment, Contact, etc.)
- **Cached Performance**: Settings are cached for fast access
- **Version Control**: Track when settings were changed

## 🚀 **How to Use**

### Accessing Settings
1. Login to your admin panel at: `http://your-domain.com/admin`
2. Navigate to **Settings** → **Booking Settings**
3. You'll see all your booking configuration options

### Changing Price Per Participant
1. Go to **Booking Settings**
2. Find "Price Per Participant" setting
3. Click **Edit** button
4. Change the value (e.g., from 20000 to 25000)
5. Click **Save**
6. Changes take effect immediately!

### Managing Payment Methods
**Enable/Disable Payment Methods:**
- Find settings like "Enable Bank Transfer", "Enable E-Wallet", "Enable Cash Payment"
- Toggle them on/off as needed

**Update Bank Account Information:**
- Edit "Bank Name", "Bank Account Number", "Bank Account Name" settings
- Changes will appear on payment pages instantly

**Update E-Wallet Accounts:**
- Edit "E-Wallet Accounts" setting (JSON format)
- Format: `{"GoPay": "0812-1234-5678", "OVO": "0812-1234-5678"}`

### Contact Information
Update office address, phone numbers, and hours through the settings panel.

## 📊 **Settings Categories**

### Pricing Settings
- **Price Per Participant**: Base price in IDR (e.g., 20000)
- **Currency**: Currency code (IDR)
- **Currency Symbol**: Display symbol (Rp)

### Payment Settings  
- **Enable Bank Transfer**: On/Off toggle
- **Enable E-Wallet**: On/Off toggle
- **Enable Cash Payment**: On/Off toggle
- **Bank Name**: Your bank name
- **Bank Account Number**: Your account number
- **Bank Account Name**: Account holder name
- **E-Wallet Accounts**: JSON object with wallet details

### General Settings
- **Payment Due Hours**: Hours to complete payment (default: 24)
- **Max Participants Per Booking**: Maximum people per booking (default: 10)
- **Booking Code Prefix**: Prefix for booking codes (BOOK-)
- **Booking Code Length**: Length of random part (5)

### Contact Information
- **Office Address**: Physical office location
- **Office Hours**: Business hours
- **Contact Phone**: Primary phone number
- **Contact Email**: Primary email address
- **WhatsApp Number**: WhatsApp contact

## 🔧 **Advanced Features**

### Cache Management
- Settings are automatically cached for performance
- Use "Clear Settings Cache" button if needed
- Cache is automatically cleared when you edit settings

### Filtering & Search
- Filter by setting group (Pricing, Payment, etc.)
- Search by setting name or key
- Filter by data type (Text, Number, True/False, JSON)

### JSON Settings
For complex settings like e-wallet accounts, use JSON format:
```json
{
  "GoPay": "0812-1234-5678",
  "OVO": "0812-1234-5678",
  "DANA": "0812-1234-5678", 
  "ShopeePay": "0812-1234-5678"
}
```

## 📋 **Common Tasks**

### Change Price to IDR 25,000
1. Find "Price Per Participant" setting
2. Edit value from `20000` to `25000`
3. Save changes

### Disable Cash Payments
1. Find "Enable Cash Payment" setting
2. Toggle to "No/False"
3. Save changes

### Update Bank Account
1. Edit "Bank Name" → Enter your bank name
2. Edit "Bank Account Number" → Enter your account number
3. Edit "Bank Account Name" → Enter account holder name
4. Save all changes

### Add New E-Wallet
1. Find "E-Wallet Accounts" setting
2. Edit the JSON to add your wallet:
```json
{
  "GoPay": "0812-1234-5678",
  "OVO": "0812-1234-5678",
  "DANA": "0812-1234-5678",
  "ShopeePay": "0812-1234-5678",
  "LinkAja": "0812-NEW-NUMBER"
}
```
3. Save changes

## 🛡️ **Best Practices**

1. **Test Changes**: Always test booking flow after making changes
2. **Backup Settings**: Export settings before major changes
3. **Use Clear Names**: Setting labels should be descriptive
4. **Valid JSON**: Ensure JSON settings are properly formatted
5. **Cache Clearing**: Use cache clear if changes don't appear immediately

## 🔍 **Troubleshooting**

**Changes not appearing on website?**
- Click "Clear Settings Cache" button in admin panel
- Refresh your website

**JSON format errors?**
- Validate JSON using online JSON validator
- Ensure proper quotes and commas

**Setting not found?**
- Check if setting is active (toggle switch)
- Verify setting key spelling

## 📈 **Migration from .env**
The system automatically uses database settings instead of .env variables. Your existing .env settings serve as fallbacks if database settings are missing.

---

## 🎯 **Quick Example: Changing Price from 20K to 30K**

1. **Login** to admin panel
2. **Navigate** to Settings → Booking Settings
3. **Find** "Price Per Participant" row
4. **Click** Edit button
5. **Change** value from `20000` to `30000`
6. **Save** changes
7. **Test** - Visit homepage, new price appears immediately!

That's it! No file editing, no server restart needed. Your new pricing is live! 🚀