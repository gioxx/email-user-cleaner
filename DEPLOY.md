# 🚀 Deployment Instructions for Email User Cleaner

This project uses **GitHub Actions** to automate the deployment of the plugin to the WordPress.org repository.

Follow this guide carefully when releasing a new version.

---

## ✏️ Step 1: Prepare the plugin for release

- Update the **Stable tag** field in `readme.txt` to the new version number (e.g., `Stable tag: 1.7`).
- Add the new version's **changelog** under the `== Changelog ==` section in `readme.txt`.
- Update the plugin's **Version** header inside the main PHP file (`email-user-cleaner.php`) if needed.
- Commit and push your changes to GitHub.

Example:

```bash
git add .
git commit -m "Prepare release 1.7"
git push
```

---

## 🏷️ Step 2: Create a GitHub Release (Tag)

- Create a new tag that matches the version number (e.g., `1.7`, `1.8.1`, etc.).
- Push the tag to GitHub.

Example:

```bash
git tag 1.7
git push origin 1.7
```

Alternatively, you can create a **Release** via the GitHub UI, specifying the tag name.

---

## 🤖 Step 3: Automatic Deployment

Once the tag is pushed:
- The GitHub Action will start automatically.
- It will:
  - Sync your plugin files to the **trunk/** directory on WordPress.org SVN.
  - Create a new SVN **tag** with the version number (e.g., `/tags/1.7`).

You don't need to do anything manually on WordPress.org.

---

## ⚠️ Important notes

- Make sure the tag name **matches exactly** the version number you set in `readme.txt` and the plugin header.
- Avoid including unnecessary files in the repository (e.g., `.github/`, `.gitignore`, `README.md` are automatically excluded by the workflow).

---

## 💬 Need help?

For any issues with deployment, check the "Actions" tab on GitHub to see the workflow logs.

---
