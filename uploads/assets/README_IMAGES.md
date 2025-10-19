# Image Assets Guide for OVAS Homepage

## Required Images

Place these images in `c:\xampp\htdocs\ovas\uploads\assets\`

### Hero Section (Carousel)
1. **hero_01.webp** (1920x900px)
   - Search: "veterinarian with dog happy clinic"
   - Source: https://unsplash.com/s/photos/veterinarian
   - Alternative: Use existing cover image temporarily

2. **hero_02.webp** (1920x900px)
   - Search: "cat checkup at vet clinic"
   - Source: https://pexels.com/search/veterinarian%20cat/
   
3. **hero_03.webp** (1920x900px)
   - Search: "vet clinic reception with pet owner"
   - Source: https://unsplash.com/s/photos/animal-hospital

### About Section
4. **about_clinic.webp** (1600x700px)
   - Search: "veterinary clinic interior clean light"
   - Source: https://unsplash.com/s/photos/veterinary-clinic

## Temporary Solution

The system will automatically fallback to existing images if these are not found:
- Hero slides will use the system cover image from settings
- About clinic will use the cover image

## Quick Setup

1. Create placeholder files (run in PowerShell):
```powershell
cd C:\xampp\htdocs\ovas\uploads\assets

# Create simple placeholder text files (for testing structure)
"Hero Image 1" | Out-File -FilePath hero_01.webp
"Hero Image 2" | Out-File -FilePath hero_02.webp  
"Hero Image 3" | Out-File -FilePath hero_03.webp
"About Clinic" | Out-File -FilePath about_clinic.webp
```

2. Later, download real images and replace these placeholders

## Recommended Image Specs

- Format: WebP (for better compression)
- Hero slides: 1920x900px, landscape
- About section: 1600x700px, landscape
- Quality: 80-85% compression
- File size: < 500KB each

## Free Image Sources

1. **Unsplash**: https://unsplash.com (High quality, free to use)
2. **Pexels**: https://pexels.com (Free stock photos)
3. **Pixabay**: https://pixabay.com (Free images and videos)

## Search Keywords

- "veterinarian with dog"
- "pet clinic interior"
- "vet examining cat"
- "animal hospital"
- "veterinary care"
- "pet wellness"
- "veterinary team"
