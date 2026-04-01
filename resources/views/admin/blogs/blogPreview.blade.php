@extends('admin.layouts.master')
@section('page_title', 'Preview')
@section('content')

<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><i class="bi bi-house-door"></i> <a href="#">Home</a></li>
        <li class="breadcrumb-item"><i class="bi bi-folder"></i> Master</li>
        <li class="breadcrumb-item active"><i class="bi bi-eye"></i> Preview</li>
    </ol>
</nav>

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center">
    <h5 class="bpv-title">
        <i class="bi bi-file-earmark-text"></i> Blog Preview
    </h5>
    <button onclick="window.print()" class="btn btn-success btn-sm">
        <i class="bi bi-printer"></i> Print
    </button>
</div>

<div class="blogv-main-box">
    <div class="blogv-wrapper">

        <!-- BLOG INFO -->
        <div class="blogv-card">
            <h6 class="blogv-heading">
                <i class="bi bi-info-circle"></i> Blog Info
            </h6>

            <div class="blogv-grid-2">
                <div>
                    <span><b><i class="bi bi-type"></i> Blog Title</b></span>
                    <p>{{ $blog->title }}</p>
                </div>

                <div>
                    <span><b><i class="bi bi-link-45deg"></i> Blog Alias</b></span>
                    <p>{{ $blog->slug }}</p>
                </div>
            </div>

            <div class="blogv-grid-2 mt-2">
                <div>
                    <span><b><i class="bi bi-tag"></i> Category</b></span>
                    <p>{{ $blog->category->category_name ?? '-' }}</p>
                </div>

                <div>
                    <span><b><i class="bi bi-star"></i> Is Featured</b></span>
                    <p>{{ $blog->is_featured == 1 ? 'Yes' : 'No' }}</p>
                </div>
            </div>
        </div>

        <!-- SHORT DESCRIPTION -->
        <div class="blogv-card">
            <h6 class="blogv-heading">
                <i class="bi bi-chat-left-text"></i> Short Description
            </h6>

            <p>{{ $blog->short_description }}</p>
        </div>

        <!-- LONG DESCRIPTION -->
        <div class="blogv-card">
            <h6 class="blogv-heading">
                <i class="bi bi-file-text"></i> Long Description
            </h6>

            <div class="blogv-content">
                {!! $blog->content !!}
            </div>
        </div>

        <!-- IMAGES -->
        <div class="blogv-card">
            <h6 class="blogv-heading">
                <i class="bi bi-images"></i> Images
            </h6>

            <!-- Thumb -->
            <div class="blogv-image-block">
                <span><b><i class="bi bi-image"></i> Thumb Image</b> (300x220)</span>
                <div class="blogv-img-box thumb hover-box">
                    <img src="{{ asset('storage/uploads/blog/'.$blog->thumb_image) }}" alt="Thumb Image">
                    <span class="blogv-hover-text">Thumb Image</span>
                </div>
            </div>

            <!-- Feature -->
            <div class="blogv-image-block mt-3">
                <span><b><i class="bi bi-image-fill"></i> Feature Image</b> (9600x420)</span>

                <div class="blogv-img-box feature hover-box">
                    <img src="{{ asset('storage/uploads/blog/'.$blog->featured_image) }}" alt="Feature Image">
                    <span class="blogv-hover-text">Feature Image</span>
                </div>
            </div>

            <!-- 🔥 META INFO -->
            <div class="blogv-meta-inline mt-3">

                <div class="meta-left">
                    <i class="bi bi-calendar-check"></i>
                    <b>Published On:</b>
                    <div class="mt-3">
                        <span>{{ date('d-M-Y H:i:s', strtotime($blog->published_at)) }}</span>
                    </div>
                </div>

                <div class="meta-right">
                    <i class="bi bi-tags"></i>
                    <b>Tags:</b>

                    <div class="blogv-tags">
                        <span>Travel</span>
                        <span>Booking</span>
                        <span>Refund</span>
                        <span>Refund</span>
                        <span>Refund</span>
                        <span>Refund</span>
                        <span>Refund</span>
                        <span>Refund</span>
                        <span>Refund</span>
                    </div>
                </div>

            </div>

        </div>

        <!-- SEO SECTION -->
        <div class="blogv-card">
            <h6 class="blogv-heading">
                <i class="bi bi-code-slash"></i> SEO / Head Section
            </h6>

            <div class="blogv-code-box">

                <!-- HEADER -->
                <div class="blogv-code-header">
                    <span><i class="bi bi-file-earmark-code"></i> HTML Head Code</span>
                    <span class="code-badge"><i class="bi bi-graph-up"></i> SEO</span>
                </div>

                <!-- CODE (ESCAPED) -->
                <pre class="blogv-code-content">
&lt;!doctype html&gt;
&lt;html lang="en"&gt;

&lt;head&gt;
  &lt;meta charset="utf-8"&gt;
  &lt;meta name="viewport" content="width=device-width, initial-scale=1.0"&gt;

  &lt;title&gt;{{ $blog->meta_title ?? $blog->title }}&lt;/title&gt;

  &lt;meta name="description" content="{{ $blog->meta_description }}"&gt;
  &lt;meta name="keywords" content="{{ $blog->meta_keywords }}"&gt;

  &lt;link rel="canonical" href="{{ $blog->canonical_url }}" /&gt;

  &lt;!-- Open Graph --&gt;
  @if(isset($blogAttributes[1]))
    @foreach($blogAttributes[1] as $attr)
      &lt;meta property="og:{{ strtolower($attr->attribute_id ?? '') }}"content="{{ $attr->attribute_value }}" /&gt;
    @endforeach
  @endif

  &lt;meta property="article:published_time"content="{{ $blog->published_at ? date('c', strtotime($blog->published_at)) : '' }}" /&gt;

  &lt;meta property="og:image"content="{{ asset('storage/uploads/blog/'.$blog->featured_image) }}" /&gt;

  &lt;!-- Twitter --&gt;
  @if(isset($blogAttributes[2]))
    @foreach($blogAttributes[2] as $attr)
      &lt;meta name="twitter:{{ strtolower($attr->attribute_id ?? '') }}"content="{{ $attr->attribute_value }}" /&gt;
    @endforeach
  @endif

  &lt;!-- Schema --&gt;
  @if(isset($blogAttributes[3]))
    @foreach($blogAttributes[3] as $attr)
      &lt;script type="application/ld+json"&gt;
      {!! $attr->attribute_value !!}
      &lt;/script&gt;
    @endforeach
  @endif

&lt;/head&gt;
</pre>

            </div>
        </div>

    </div>
</div>

<!-- BACK BUTTON --><div class="mt-4" style="width:67%; margin: 0 auto;">

    <div class="d-flex justify-content-between align-items-center">

        <!-- LEFT: BACK -->
        <div>
            <a href="{{ url()->previous() }}" class="bpv-back-btn">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>

        <!-- RIGHT: ACTION BUTTONS -->
        <div>
            <form method="POST" action="{{ route('blogs.updateStatus') }}" class="d-flex gap-2">
                @csrf
                <input type="hidden" name="blog_id" value="{{ Crypt::encryptString($blog->id) }}">

                <button type="submit" name="status" value="0" class="bpv-back-btn bg-secondary text-white border-0">
                    Save as Draft
                </button>

                <button type="submit" name="status" value="1" class="bpv-back-btn bg-success text-white border-0">
                    Publish
                </button>
            </form>
        </div>

    </div>

</div>

@endsection