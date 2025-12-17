## 🔧 Gmail SMTP Configuration Guide

### Problem: "SMTP Error: Could not authenticate"

This happens when Gmail credentials are invalid or 2-Factor Authentication (2FA) is not set up.

---

## ✅ Solution: Create a Gmail App Password

### Step 1: Enable 2-Factor Authentication (if not already enabled)
1. Go to: https://myaccount.google.com/
2. Click **Security** (left menu)
3. Scroll to **2-Step Verification**
4. Click **Get Started** and follow the steps

### Step 2: Generate App Password
1. Go to: https://myaccount.google.com/apppasswords
2. You'll see a dropdown for **Select the app** and **Select the device**
   - App: Select **Mail**
   - Device: Select **Windows Computer** (or your device)
3. Google will generate a **16-character password** like: `rnjt hmev gklb rpmv`
4. **Copy this password** (without spaces when entering)

### Step 3: Update config/mailer.php
Open `c:\xampp\htdocs\yerabby\config\mailer.php` and update:

```php
$mail->Username = 'your-email@gmail.com';     // Your full Gmail address
$mail->Password = 'rnjt hmev gklb rpmv';      // Your 16-char App Password
```

**Example:**
```php
$mail->Username = 'personinkonnu@gmail.com';
$mail->Password = 'rnjt hmev gklb rpmv';      // Replace with your App Password
```

### Step 4: Test
1. Go to: http://localhost/yerabby/view/general/forgot-password.php
2. Enter a test email
3. Check if email is sent successfully

---

## 🐛 Troubleshooting

| Error | Solution |
|-------|----------|
| "Could not authenticate" | App Password is wrong - regenerate it |
| "Less secure app" | 2FA not enabled - follow Step 1 |
| Email not received | Check spam folder, or test on production server |
| "Connection timed out" | Gmail might be blocking - allow less secure apps |

---

## 🔐 Important Notes
- **Never share your App Password** - treat it like your real password
- **App Passwords are different from your Gmail password**
- Each app can have its own password for security
- If compromised, you can delete the App Password and create a new one

---

## 📧 For Production (Real Server)
After deploying to your production server:
1. Create a Gmail App Password again
2. Update the credentials in `config/mailer.php`
3. Test the forgot password flow

**Your current credentials in mailer.php:**
- Email: personinkonnu@gmail.com
- Password: rnjt hmev gklb rpmv (appears to be invalid - regenerate)
