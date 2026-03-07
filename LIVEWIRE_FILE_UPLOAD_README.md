# Livewire File Upload Components

This project now includes comprehensive Livewire file upload components that can be used by both garage side and business owner side for image and file uploads.

## Available Components

### 1. ImageUpload Component
Specialized component for image uploads only.

**Features:**
- Image-only file validation
- Multiple image support
- Drag & drop functionality
- Image previews
- Responsive design

**Usage:**
```php
@livewire('image-upload', [
    'name' => 'images',
    'maxFiles' => 5,
    'required' => false,
    'showPreview' => true,
    'previewSize' => '100px'
])
```

### 2. FileUpload Component
Versatile component that handles multiple file types.

**Supported File Types:**
- `image`: JPEG, PNG, JPG, WebP, GIF
- `video`: MP4, AVI, MOV, WMV, FLV
- `audio`: MP3, WAV, OGG, M4A
- `document`: PDF, DOC, DOCX, TXT
- `all`: All supported file types

**Usage:**
```php
@livewire('file-upload', [
    'name' => 'files',
    'fileType' => 'video', // 'image', 'video', 'audio', 'document', 'all'
    'maxFiles' => 3,
    'required' => false,
    'showPreview' => true,
    'previewSize' => '100px'
])
```

### 3. SparePartImageUpload Component
Compact component designed specifically for spare parts forms.

**Features:**
- Compact design for forms
- Multiple image support
- Part-specific indexing
- Event emission for parent components

**Usage:**
```php
@livewire('spare-part-image-upload', [
    'name' => 'part_images',
    'partIndex' => 0,
    'maxFiles' => 5,
    'compactMode' => true
])
```

### 4. SparePartsForm Component
Complete form component with dynamic rows and image uploads.

**Features:**
- Dynamic spare part rows
- Integrated image uploads
- Form validation
- Auto-cleanup of temporary files

**Usage:**
```php
@livewire('spare-parts-form')
```

## Implementation Examples

### Insurance Create File (Garage Side)
Replace the existing file inputs in `resources/views/insurance/create-file.blade.php`:

```php
<!-- Images -->
@livewire('image-upload', [
    'name' => 'images', 
    'fileType' => 'image', 
    'maxFiles' => 5, 
    'required' => false
])

<!-- Videos -->
@livewire('file-upload', [
    'name' => 'videos', 
    'fileType' => 'video', 
    'maxFiles' => 3, 
    'required' => false
])

<!-- Audio -->
@livewire('file-upload', [
    'name' => 'audios', 
    'fileType' => 'audio', 
    'maxFiles' => 3, 
    'required' => false
])
```

### Business Owner Create File
Replace the existing spare parts image inputs:

```php
<!-- For individual spare part rows -->
@livewire('spare-part-image-upload', [
    'name' => 'parts[0][photo_data]',
    'partIndex' => 0,
    'maxFiles' => 5,
    'compactMode' => true
])

<!-- Or use the complete form component -->
@livewire('spare-parts-form')
```

## Component Properties

### Common Properties
- `name`: Form field name
- `maxFiles`: Maximum number of files allowed
- `required`: Whether the field is required
- `showPreview`: Whether to show file previews
- `previewSize`: Size of preview thumbnails

### ImageUpload Specific
- `acceptedTypes`: Array of accepted MIME types
- `maxFileSize`: Maximum file size in KB

### FileUpload Specific
- `fileType`: Type of files to accept ('image', 'video', 'audio', 'document', 'all')

### SparePartImageUpload Specific
- `partIndex`: Index for the spare part
- `compactMode`: Whether to use compact styling

## File Storage

All uploaded files are temporarily stored in:
- `storage/app/temporary/livewire/` - For general uploads
- `storage/app/temporary/spare-parts/` - For spare part images

Files are automatically cleaned up when:
- Individual files are removed
- All files are cleared
- Components are destroyed

## Validation

Components include built-in validation:
- File type validation
- File size validation
- Required field validation
- Custom error messages

## Events

Components emit events that can be listened to:
- `imagesUpdated`: When images are updated in SparePartImageUpload
- File validation errors are displayed inline

## Styling

Components use Bootstrap 5 classes and include:
- Responsive design
- Hover effects
- Drag & drop visual feedback
- Error state styling

## Browser Support

- Modern browsers with ES6+ support
- Drag & drop functionality
- File API support
- Image preview support

## Dependencies

- Laravel 8+
- Livewire 2+
- Bootstrap 5
- Boxicons (for icons)

## Troubleshooting

### Common Issues

1. **Files not uploading**: Check file permissions and storage configuration
2. **Validation errors**: Ensure file types and sizes are within limits
3. **Preview not showing**: Check if Storage facade is properly configured
4. **Component not rendering**: Ensure Livewire is properly installed and configured

### Debug Mode

Enable Livewire debug mode in `.env`:
```
LIVEWIRE_DEBUG=true
```

## Migration from FilePond

If you're migrating from FilePond:

1. Replace FilePond inputs with Livewire components
2. Remove FilePond JavaScript initialization
3. Update form handling to work with Livewire
4. Test file uploads and validation

## Performance Considerations

- Files are stored temporarily and should be moved to permanent storage
- Large files may impact performance
- Consider implementing chunked uploads for very large files
- Use appropriate file size limits for your use case

## Security

- File type validation prevents malicious uploads
- File size limits prevent abuse
- Temporary storage with automatic cleanup
- CSRF protection through Livewire

## Future Enhancements

- Progress bars for large file uploads
- Image compression and optimization
- Cloud storage integration
- Advanced file management interface
- Batch operations support
