# 🔒 Security Checklist - Before GitHub Push

## ✅ Pre-push Security Verification

### 1. Environment Variables
- [x] `.env` file contains real DB credentials
- [x] `.env` is listed in `.gitignore` (protected)
- [x] `.env.example` uses placeholder values (safe to commit)
- [x] No credentials in `.env` are hardcoded in any `.php` files

### 2. Sensitive Files
- [x] `_apuntes/` directory ignored (contains test scripts)
- [x] `storage/documents/*` ignored (user uploads)
- [x] No SQL dumps or database backups tracked
- [x] No IDE configuration files exposed (`.idea/`, `.vscode/`)

### 3. Code Review
- [x] No hardcoded database passwords in code
- [x] No API keys or tokens in source files
- [x] No plaintext credentials in comments
- [x] PDO uses prepared statements (SQL injection protected)
- [x] Password hashing uses `password_hash()` (never plaintext)

### 4. Configuration Files
- [x] `.gitignore` includes all sensitive patterns:
  - Environment files: `.env*`
  - IDE files: `.vscode/`, `.idea/`
  - OS files: `.DS_Store`, `Thumbs.db`, `*.Zone.Identifier`
  - Build/temp: `/vendor/`, `/node_modules/`, `*.log`
  - Database: `*.sql.bak`, `*.dump`
- [x] No overly permissive file permissions in repo

### 5. Logs & Debugging
- [x] `APP_DEBUG=true` only in `.env.example` (not in prod)
- [x] No error logs committed
- [x] No debug output in comments

### 6. Dependencies
- [x] No `composer.lock` committed (each dev installs own version)
- [x] `vendor/` directory ignored
- [x] No unverified third-party code checked in

## 🚀 Safe to Push Commands

```bash
# Final verification before push
git status                          # Verify no .env or sensitive files
git diff --cached                   # Review staging area
git log --oneline -10               # Verify history clean

# Add all non-sensitive files
git add -A
git status                          # Double-check staging

# Commit and push
git commit -m "feat: MVP ready for production"
git push origin main
```

## 📝 Production Deployment Notes

1. **Set environment variables on server:**
   ```bash
   cp .env.example .env
   # Edit .env with REAL production credentials
   chmod 600 .env  # Read-only for web server
   ```

2. **Update config.php to require .env file:**
   ```php
   if (!file_exists(__DIR__ . '/../.env')) {
       die('ERROR: .env file not found. Copy .env.example to .env and configure.');
   }
   ```

3. **Set `APP_DEBUG=false` in production .env**

4. **Restrict storage directory permissions:**
   ```bash
   chmod 755 storage/
   chmod 755 storage/documents/
   chown www-data:www-data storage/
   ```

## ✅ Verification Summary

| Category | Status | Notes |
|----------|--------|-------|
| Environment Files | ✅ Protected | .env in .gitignore, credentials safe |
| Source Code | ✅ Safe | No hardcoded credentials |
| Configuration | ✅ Secure | .gitignore comprehensive |
| Dependencies | ✅ Safe | vendor/ and composer.lock ignored |
| Sensitive Data | ✅ Hidden | All temporary files ignored |

**Ready for GitHub Push: YES** ✅
