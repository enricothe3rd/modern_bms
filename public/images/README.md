# Logo Configuration

## How to add your organization logo to PDFs:

1. Place your logo image file in this directory (`public/images/`)
2. Update your `.env` file with the correct path:
   ```
   PDF_LOGO_PATH=images/your-logo.png
   ```
3. Supported formats: PNG, JPG, JPEG, GIF
4. Recommended size: Maximum 200px width, 80px height for best results

## Example:
If your logo file is named `company-logo.png`, set:
```
PDF_LOGO_PATH=images/company-logo.png
```

The logo will automatically appear at the top of your PDF documents.