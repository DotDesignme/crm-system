# Security Policy 🛡️

This CRM project is designed with a **Privacy-First** approach for its open-source release. 

## 🔒 Automated Protection
We have implemented a **Git Pre-commit Hook** system. This system automatically scans every file before it is committed to GitHub to detect and block any sensitive data, including:
- Private Server IP Addresses.
- Authentication Credentials and Passwords.
- Original Deployment Scripts (used for private workflows).

## 🛡️ Safe Contributions
If you wish to contribute to this project:
1. Ensure your `.env` file is excluded (already in `.gitignore`).
2. Use `.example` files for any new configuration or deployment scripts.
3. If the automated security hook blocks your commit, it means sensitive data was detected. Please replace it with placeholders before trying again.

## 📝 Reporting Vulnerabilities
If you discover a security vulnerability within this CRM, please do not open a public issue. Instead, report it privately to the project maintainers.

---
*Your data security is our top priority.*
