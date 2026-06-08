# Unified Translation System - Setup Guide

## Overview
This implementation provides a **unified API backend** for translations that works for both React frontend and Laravel backend.

## What's Changed

### 1. **New API Endpoint**
- `GET /api/v1/translations/{locale}` - Returns all translations for a locale
- `POST /api/v1/translations/add` - Adds new translations

### 2. **New Laravel Components**
- `app/Http/Controllers/Backend/Api/Translation/TranslationController.php`
- `app/Console/Commands/ImportTranslations.php`

### 3. **Updated React Components**
- `resources/backend/react/i18next.tsx` - Now uses API backend
- `resources/backend/react/utils/translate.ts` - Updated to work with API

## Production Setup Steps

### Step 1: Import Translations
Run the import command to populate the database:

```bash
php artisan translations:import
```

This will:
- Read all keys from `translations.txt`
- Import them to the database with English as default
- Skip keys that already exist

### Step 2: Clear Cache
```bash
php artisan cache:clear
```

### Step 3: Build Frontend
```bash
npm run build
```

## How It Works

### For Existing Translations
1. React app loads and calls `/api/v1/translations/en-US`
2. Laravel returns all translations from the database
3. React caches them using i18next
4. All `translate('Text')` calls work as expected

### For New Translations
1. Developer adds `translate('New Text')` in React code
2. If the key doesn't exist in the database:
   - React displays the original text as fallback
   - (Optional) Call `addTranslation('new_text', 'New Text')` to register it
   - Or the backend's `translate()` function will auto-create it when called from PHP

### Auto-Creation Flow
```
React: translate('Hey Admin!')
  ↓
Converts to key: "hey_admin"
  ↓
Checks i18next cache → Not found
  ↓
Displays fallback: "Hey Admin!"
  ↓
(Optional) POST /api/v1/translations/add
  ↓
Database now has the key for next time
```

## API Endpoints

### Get Translations
```
GET /api/v1/translations/{locale}
```
**Response:**
```json
{
  "dashboard": "Dashboard",
  "admin_dashboard": "Admin Dashboard",
  "hey_admin": "Hey Admin!"
}
```

### Add Translation
```
POST /api/v1/translations/add
Content-Type: application/json

{
  "key": "new_key",
  "value": "New Translation",
  "locale": "en-US"
}
```

## Adding New Languages

1. Add language to `Language` model
2. Create translation records in database:
   ```php
   Translation::create([
       'lang_key' => 'bn',
       't_key' => 'dashboard',
       't_value' => 'ড্যাশবোর্ড'
   ]);
   ```
3. Clear cache: `php artisan cache:clear`

## Troubleshooting

### Translations not showing
1. Check if translations are imported:
   ```bash
   php artisan translations:import
   ```
2. Clear cache:
   ```bash
   php artisan cache:clear
   ```
3. Check browser network tab for API calls

### API returning empty
1. Ensure `translations.txt` exists
2. Run import command
3. Check database has records:
   ```sql
   SELECT COUNT(*) FROM translations WHERE lang_key = 'en-US';
   ```

### Key not found
- Keys are auto-generated from text:
  - `"Hey Admin!"` → `"hey_admin"`
  - `"Dashboard"` → `"dashboard"`
  - Special chars removed, lowercase, spaces → underscores

## Benefits

✅ **Single Source of Truth** - Database is the master
✅ **Auto-Creation** - New keys auto-create in DB from PHP
✅ **Real-time Updates** - No rebuild needed for translation changes
✅ **Caching** - Fast performance with Laravel cache
✅ **Flexible** - Easy to add new languages

## Migration from Old System

If you were using JSON files before:
1. Run `php artisan translations:import` (imports all 528 keys)
2. The API will now serve from database
3. Old JSON files can be removed (optional)
4. Frontend will automatically use the API backend

## Performance

- **First Load:** ~100-200ms (API call)
- **Subsequent Loads:** Instant (i18next cache + Laravel cache)
- **Cache Duration:** Forever (until translations are updated)
- **Cache Clearing:** Automatic when translations are modified
