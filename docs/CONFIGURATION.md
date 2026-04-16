# External Services Configuration Guide

This document provides instructions on how to configure third-party services used by the TA Submission System, specifically **Sentry** for error tracking and **Cloudflare R2** for off-site file storage.

---

## 🛡️ Sentry Integration

We use Sentry to monitor errors and performance in real-time. This helps us identify and fix issues before they impact students or faculty members.

### Setup Instructions

1.  **Get a DSN**:
    - Sign in to your [Sentry.io](https://sentry.io) account.
    - Create a new Project (choose **Laravel** as the platform).
    - Go to **Project Settings** > **Client Keys (DSN)**.
    - Copy the **DSN** URL.

2.  **Update `.env`**:
    Add the DSN to your environment variables:
    ```dotenv
    SENTRY_LARAVEL_DSN=https://your-dsn-here@sentry.io/project-id
    SENTRY_TRACES_SAMPLE_RATE=1.0
    SENTRY_SEND_DEFAULT_PII=true
    ```

### Configuration Details
- `SENTRY_LARAVEL_DSN`: The unique identifier for your Sentry project.
- `SENTRY_TRACES_SAMPLE_RATE`: Controls the percentage of transactions sent to Sentry for performance monitoring (1.0 = 100%).
- `SENTRY_SEND_DEFAULT_PII`: If set to `true`, Sentry will include personally identifiable information (like the authenticated user's ID) in error reports, which is helpful for debugging.

---

## ☁️ Cloudflare R2 Storage

The system supports Cloudflare R2 for storing submission files (proposals, final documents, etc.). R2 is an S3-compatible object storage service that offers zero egress fees.

### Setup Instructions

1.  **Create an R2 Bucket**:
    - Log in to the [Cloudflare Dashboard](https://dash.cloudflare.com).
    - Navigate to **R2** > **Create Bucket**.
    - Name your bucket (e.g., `ta-submission`).

2.  **Generate API Credentials**:
    - In the R2 side menu, click **Manage R2 API Tokens**.
    - Create a new token with **Edit** permissions for the specific bucket.
    - Copy the **Access Key ID** and **Secret Access Key**.

3.  **Find your Account ID**:
    - Your Account ID is visible on the R2 Overview page.

4.  **Update `.env`**:
    Configure the environment variables to use R2:
    ```dotenv
    # Required for R2
    CLOUDFLARE_R2_ACCESS_KEY_ID=your-access-key-id
    CLOUDFLARE_R2_SECRET_ACCESS_KEY=your-secret-access-key
    CLOUDFLARE_R2_BUCKET=ta-submission
    CLOUDFLARE_R2_ACCOUNT_ID=your-cloudflare-account-id
    CLOUDFLARE_R2_URL=https://pub-your-bucket-id.r2.dev # Optional: Custom/Public URL

    # Set as default (Optional)
    FILESYSTEM_DISK=r2
    ```

### Key Concepts
- **Driver**: The system uses the `s3` driver to communicate with R2, as R2 is fully S3-compatible.
- **Region**: Set to `auto` in `config/filesystems.php`.
- **Signed URLs**: The system supports temporary pre-signed URLs for secure file downloads directly from R2.

### Local vs. Cloud Storage
By default, files are stored on the `local` disk (`storage/app/private`). To switch to R2 for all new uploads, set `FILESYSTEM_DISK=r2`. Existing files can be migrated via the API if needed.
