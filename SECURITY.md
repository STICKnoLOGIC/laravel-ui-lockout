# Security Policy

## Supported Versions

| Version | Supported          | Laravel Version |
| ------- | ------------------ | --------------- |
| 1.x     | :white_check_mark: | 10.x, 11.x, 12.x |

## Framework Requirements

**IMPORTANT:** This package ONLY supports Laravel framework.

Using this package with any other framework (CodeIgniter, Symfony, Yii, etc.) is:
- ❌ Not supported
- ❌ Not tested
- ❌ Will throw `FrameworkNotSupportedException`

## Security Features

### Built-in Protection

✅ **XSS Prevention**
- All user inputs are sanitized
- Component IDs: alphanumeric + hyphens only
- CSS classes: no script injection allowed
- Output is properly escaped in Blade templates

✅ **Path Traversal Prevention**
- Custom view paths are strictly validated
- Blocks `../`, `./`, and `\\` characters
- Only dot notation allowed (e.g., `folder.view`)
- Alphanumeric + dots/hyphens/underscores only

✅ **SSL/TLS Enforcement**
- SSL certificate verification is ALWAYS enabled
- No option to disable SSL in production
- Secure HTTPS connections enforced for API calls

✅ **API Security**
- Response structure validation
- Timeout protection with retry limits
- Cached responses to reduce external calls
- Fallback to safe default messages

✅ **Input Validation**
- Expiration type validated against whitelist
- Date parsing with error handling
- API response structure validation

✅ **Environment Awareness**
- Production-specific security measures
- Enhanced logging in development
- Different behavior per environment

## Reporting a Vulnerability

If you discover a security vulnerability, please email:

**Security Contact:** johnaerial.azcune@sticknologic.is-a.dev

Please include:
- Description of the vulnerability
- Steps to reproduce
- Potential impact
- Suggested fix (if any)

### Response Timeline

- **Acknowledgment:** Within 48 hours
- **Initial Assessment:** Within 7 days
- **Fix Development:** Within 30 days (depending on severity)
- **Public Disclosure:** After fix is released

## Security Best Practices

### Production Configuration

1. **Set Expiration Date**
```php
// config/ui-lockout.php
'expires_at' => '2026-12-31',
```

2. **Configure Lockout Messages**
```php
// config/ui-lockout.php
'title' => 'Access Restricted',
'message' => 'Contact support@example.com for assistance.',
```

3. **API Configuration (if used)**
```php
// config/ui-lockout.php
'enable_api' => true,
'api_url' => 'https://api.example.com/lockout-message',
'api_timeout' => 5,
'api_retries' => 2,
'cache_duration' => 600,
```

4. **Validate Custom Views**
```php
// Good - dot notation
'custom_view' => 'lockout.custom'

// Bad - path traversal (will be blocked)
'custom_view' => '../../../etc/passwd'  // ❌ Blocked
'custom_view' => 'folder/view'          // ❌ Blocked
```

5. **Monitor Logs**
```bash
# Check for security warnings
tail -f storage/logs/laravel.log | grep "UI Lockout"
```

### API Security

1. **Use HTTPS Only**
```php
// config/ui-lockout.php
'api_url' => 'https://api.example.com/lockout-message',  // ✅ Good
// 'api_url' => 'http://api.example.com',  // ❌ Bad (insecure)
```

2. **Set Reasonable Timeouts**
```php
// config/ui-lockout.php
'api_timeout' => 5,   // seconds
'api_retries' => 2,   // retry attempts
```

3. **Validate API Responses**
API must return JSON with `message` field:
```json
{
    "message": "Your lockout message here"
}
```

## Known Security Considerations

### 1. Gibberish Feature
The `htmlToGibberish()` method uses `{!! !!}` (unescaped output) to display obfuscated HTML. This is intentional for the obfuscation feature but requires the opacity to be > 0.8 to activate.

**Mitigation:** Only activated at high opacity levels where lockout is nearly complete.

### 2. API Endpoints
External API calls can be a vector for attacks.

**Mitigations:**
- SSL certificate verification enforced
- Timeout protection
- Retry limits
- Response validation
- Response caching to reduce external calls
- Fallback to safe default message

### 3. Custom Views
Including custom views can be risky if paths are not validated:

**Mitigations:**
- Strict path validation
- Path traversal prevention
- Whitelist of allowed characters
- View existence check before inclusion
- Logging of suspicious attempts (dev only)

## Compliance

This package follows:
- ✅ OWASP Top 10 security guidelines
- ✅ Laravel security best practices
- ✅ PHP security recommendations
- ✅ Semantic Versioning (SemVer)

## Security Updates

Security patches will be released as:
- **Critical:** Immediate patch release
- **High:** Within 7 days
- **Medium:** Next minor version
- **Low:** Next major version

## Disclosure Policy

We follow responsible disclosure:
1. Vulnerability reported privately
2. Acknowledgment sent
3. Fix developed and tested
4. Security advisory published
5. Fix released
6. Public disclosure after fix is available

## Attribution

We thank security researchers who report vulnerabilities responsibly.

## Contact

For security concerns: johnaerial.azcune@sticknologic.is-a.dev
For general issues: Use GitHub Issues

---

Last Updated: February 21, 2026
