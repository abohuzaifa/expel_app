<!-- resources/views/upload-form.blade.php -->
<form action="{{ route('upload.images') }}" method="post" enctype="multipart/form-data">
    @csrf
    <input type="file" name="images[]" multiple>
    <button type="submit">Upload Images</button>
</form>