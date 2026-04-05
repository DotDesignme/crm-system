# Local Development Guide 💻

This guide explains how to maintain your local functional environment while keeping the GitHub repository clean and open-source.

## ⚙️ How it Works

1. **Private Files:** Your local functional scripts (`auto_deploy.sh`, `server_deploy.sh`, `build_and_zip.sh`) and database (`database/database.sqlite`) are added to `.gitignore`. They will **never** be pushed to GitHub.
2. **Public Templates:** We use `.example` files (e.g., `auto_deploy.sh.example`) to share the structure with other users without sharing your private data.

## 🔄 Workflow for Updates

If you change the logic in your local `auto_deploy.sh` and want the public version to have those changes:

1. Manually copy the new logic (without the IP/Password) to `auto_deploy.sh.example`.
2. Commit the `.example` file.

## 🛡️ Git Security Hook

We have installed a local Git hook that scans your staged files for:

- Your Server IP: `[YOUR_SERVER_IP]`
- Your Root Password: `[YOUR_ROOT_PASSWORD]`

If you accidentally try to commit a file containing these, **Git will block the commit**.

### To Re-install the Hook (if needed)

If you move the project or the hook stops working, run:

```bash
chmod +x scripts/git-hooks/pre-commit
ln -sf "../../scripts/git-hooks/pre-commit" .git/hooks/pre-commit
```

---
*Keep developing safely!*
