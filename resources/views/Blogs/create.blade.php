@extends(backpack_view('blank'))
<!DOCTYPE html>
@if(session()->has('message'))
        <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{!! session()->get('message') !!}</div>
@endif

@php

    use Illuminate\Support\Arr;
    use App\Models\Blog;
    use App\Models\ImageFile;

    $defaultBreadcrumbs = [
        trans('backpack::crud.admin') => url(config('backpack.base.route_prefix'), 'dashboard'),
        $crud->entity_name_plural => url($crud->route),
        trans('backpack::crud.add') => false,
    ];
    $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
    $hasDropzone = Arr::first(
        $crud->fields(),
        function ($item) {
            return $item['type'] === 'dropzone';
        },
        [],
    );

    $id = request()->id;
    $blog = Blog::where('id', $id)->first();
    $model = 'App\Models\Blog';
    $image_file = ImageFile::where('parent_id', $id)->where('parent_type',$model)->first();
    // dd($entry->getKey());
@endphp

<style>
    .preview-image {
        /* width: 100%; */
        height: 10rem;
        object-fit: cover;
        border-radius: 1rem;
    }
    .image {
        object-fit: cover;
        height: 3rem;
        cursor: pointer;
    }
</style>

@section('header')
    <section class="header-operation container-fluid animated fadeIn d-flex mb-2 align-items-baseline d-print-none"
        bp-section="page-header">
        <h1 class="text-capitalize mb-0" bp-section="page-heading">{!! $crud->getHeading() ?? $crud->entity_name_plural !!}</h1>
        <p class="ms-2 ml-2 mb-0" bp-section="page-subheading">{!! $crud->getSubheading() ?? trans('backpack::crud.add') . ' ' . $crud->entity_name !!}.</p>
        @if ($crud->hasAccess('list'))
            <p class="mb-0 ms-2 ml-2" bp-section="page-subheading-back-button">
                <small>
                    <a href="{{ url($crud->route) }}" class="d-print-none font-sm">
                        <span><i
                                class="la la-angle-double-{{ config('backpack.base.html_direction') == 'rtl' ? 'right' : 'left' }}"></i>
                            {{ trans('backpack::crud.back_to_all') }} <span>{{ $crud->entity_name_plural }}</span></span>
                    </a>
                </small>
            </p>
        @endif
    </section>
@endsection

@section('content')
    <div class="row" bp-section="crud-operation-create">
        <div class="{{ $crud->getCreateContentClass() }}">

            @include('crud::inc.grouped_errors')
            <form action="{{ url($crud->route) }}" @if($id) action="{{url($crud->route.'/'.$entry->getKey())}}" @endif method="POST" enctype="multipart/form-data">
                @csrf
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <input type="text" name="id" @if($id) value="{{ $blog->id }}" @endif hidden>
                            <div class="col-sm-12">
                                <div class="form-group row mt-2">
                                    <label for="title" class="col-form-label">Title</label>
                                    <input type="text" name="title" value="{{ $blog->title ?? ''}}" class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group row mt-2">
                                    <label for="body" class="col-form-label">Body</label>
                                    <textarea class="form-control tinymce-editor" cols="30" rows="8" name="body">{!! $blog->body ?? '' !!}</textarea>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group row mt-2">
                                    <label for="solution" class="col-form-label">Solution</label>
                                    <textarea class="form-control tinymce-editor" cols="30" rows="8" name="solution">{!! $blog->solution ?? '' !!}</textarea>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group row mt-2">
                                    <label  class="col-form-label">upload image</label>
                                    <div class="upload-image">
                                        <img src="{{ asset('images/add.png') }}" id="add_first_image" class="image display-none">
                                        <input type="file" class="form-control" id="upload_first_image" name="images" hidden>
                                        <img @if ($id) src="{{asset($image_file->file_path)}}" @endif class="preview-image" id="preview_first_image" style="cursor: pointer;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @include('crud::inc.form_save_buttons')
            </form>
        </div>
    </div>
@endsection

@section('after_scripts')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.tiny.cloud/1/ui41xm5og1ddcipj3m3rprllqaik7e0g21k333juij2uw3h0/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>

    <script type="text/javascript">

        tinymce.init({
            selector: 'textarea.tinymce-editor',
            height: 300,
            width: 700,
            menubar: false,
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table paste code help wordcount',
            ],
            toolbar: 'undo redo | formatselect | ' +
                'bold italic backcolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | help',
            content_css: '//www.tiny.cloud/css/codepen.min.css'
        });

        $(document).ready(function(){
            const uploadFrontImage = document.getElementById('upload_first_image');
            const addFrontImage = document.getElementById('add_first_image');
            const previewFrontImage = document.getElementById('preview_first_image');

            addFrontImage.addEventListener('click', () => {
            uploadFrontImage.click();
            });

            previewFrontImage.addEventListener('click', () => {
            uploadFrontImage.click();
            });

            uploadFrontImage.addEventListener('change', () => {
            const file = uploadFrontImage.files[0];
            const reader = new FileReader();

            reader.addEventListener('load', () => {
                addFrontImage.classList.add('display-none');
                previewFrontImage.classList.remove('display-none');
                previewFrontImage.src = reader.result;
            });
            reader.readAsDataURL(file);
            });
        });

    </script>
@endsection

