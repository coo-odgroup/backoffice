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
                    <p>Lorem ipssum gosym</p>
                </div>

                <div>
                    <span><b><i class="bi bi-link-45deg"></i> Blog Alias</b></span>
                    <p>lorem-ipsum-gypsum</p>
                </div>
            </div>

            <div class="blogv-grid-2 mt-2">
                <div>
                    <span><b><i class="bi bi-tag"></i> Category</b></span>
                    <p>-</p>
                </div>

                <div>
                    <span><b><i class="bi bi-star"></i> Is Featured</b></span>
                    <p>No</p>
                </div>
            </div>
        </div>

        <!-- SHORT DESCRIPTION -->
        <div class="blogv-card">
            <h6 class="blogv-heading">
                <i class="bi bi-chat-left-text"></i> Short Description
            </h6>

            <p>
                Before we get into the Indian Railways quota system, it is important to understand why it exists. Demand for train travel is dynamic, often peaking on certain routes and at certain times, such as during festivals and school holidays. During these times,
            </p>
        </div>

        <!-- LONG DESCRIPTION -->
        <div class="blogv-card">
            <h6 class="blogv-heading">
                <i class="bi bi-file-text"></i> Long Description
            </h6>

            <div class="blogv-content">

                <p>
                    Booking train tickets in advance gives passengers predictability regarding travel dates and times, and the majority of tickets are now booked online. However, in the past, tickets could be booked up to 120 days before travel; this window is currently only 60 days, making it even more imperative that passengers know how to get train ticket confirmation, especially on busy routes and during peak travel times such as holidays, festive periods, and long weekends.
                </p>

                <p>
                    Read on to learn all about how you can get a train ticket confirmation in India and the quotas that you can use to ensure your ticket gets confirmed.
                </p>

                <h6><i class="bi bi-diagram-3"></i> Indian Railways Quota System</h6>

                <p>
                    Before we get into the Indian Railways quota system, it is important to understand why it exists. Demand for train travel is dynamic, often peaking on certain routes and at certain times, such as during festivals and school holidays. During these times, tickets are frequently sold out in advance, making it difficult for passengers to reliably plan for emergencies or last-minute travel.
                </p>

                <p>
                    Also, given the sheer volume of passengers who use trains daily, there may be instances when certain categories of travellers (such as senior citizens, pregnant women, or defence personnel) cannot secure a train ticket confirmation in time for their journey. These problems mean many travellers have to face the question: “Will my train ticket get confirmed?”
                </p>

                <p>
                    To address these issues, the Indian Railways uses a quota-based allocation system to distribute seats and berths across different passenger categories. This system can ensure that passengers have the option to explore various booking categories if they meet the qualifying criteria, increasing the odds of them getting a confirmed ticket. The quotas available include:
                </p>

                <ul>
                    <li><i class="bi bi-check-circle"></i> <b>General quota:</b> This is the standard booking category and is available to all citizens.</li>
                    <li><i class="bi bi-lightning"></i> <b>Tatkal quota:</b> Tatkal is meant for urgent travel. It opens one day before the journey from the originating station.</li>
                    <li><i class="bi bi-currency-rupee"></i> <b>Premium Tatkal quota:</b> Offers confirmed tickets only with dynamic pricing.</li>
                    <li><i class="bi bi-person-heart"></i> <b>Ladies quota:</b> For women travelling alone or with children.</li>
                    <li><i class="bi bi-person-badge"></i> <b>Lower berth quota:</b> For senior citizens and pregnant women.</li>
                </ul>

                <p>
                    Understanding the quota system is important because a train that looks unavailable in one quota may still show options in another valid category, helping you get a confirmed ticket.
                </p>

                <h6><i class="bi bi-lightbulb"></i> Tips & Tricks to Secure a Confirmed Train Ticket</h6>

                <p>
                    While understanding the quota system is one way to increase your odds of a train ticket confirmation, there are other strategies that you could employ as well.
                </p>

                <h6><i class="bi bi-clock"></i> Book as soon as the reservation window opens</h6>

                <p>
                    You can book train tickets starting from up to 60 days before the journey date. Delaying beyond this date reduces your chances of getting a confirmed ticket, as seats may sell out. Even if you’re unsure about your travel plans, booking via redRail with the Free Cancellation service lets you cancel your train ticket for free and receive a 100% refund within 5-8 working days.
                </p>

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
                    <img src="{{ asset('assets/img/thumb_image.jpg') }}" alt="Thumb Image">
                    <span class="blogv-hover-text">Thumb Image</span>
                </div>
            </div>

            <!-- Feature -->
            <div class="blogv-image-block mt-3">
                <span><b><i class="bi bi-image-fill"></i> Feature Image</b> (9600x420)</span>

                <div class="blogv-img-box feature hover-box">
                    <img src="{{ asset('assets/img/feature_image.jpg') }}" alt="Feature Image">
                    <span class="blogv-hover-text">Feature Image</span>
                </div>
            </div>

            <!-- 🔥 META INFO -->
            <div class="blogv-meta-inline mt-3">

                <div class="meta-left">
                    <i class="bi bi-calendar-check"></i>
                    <b>Published On:</b>
                    <div class="mt-3">
                        <span>24-Apr-2026 10:22:00</span>
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
  &lt;meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0"&gt;
  &lt;title&gt;DreamsTour - Travel and Tour Booking Angular 19 template&lt;/title&gt;
  &lt;meta name="description" content="DreamsTour - A premium Angular 19 template crafted for travel and tour booking. Tailored for travel agencies and booking platforms, it features flight, hotel, and tour reservations, and holiday packages."&gt;
  &lt;meta name="keywords" content="travel booking template, tour booking, Angular 19 travel template, DreamsTour, hotel booking, flights booking, holiday packages, tour agency website, travel agency template, travel HTML template, booking system, responsive travel template, Bootstrap travel website"&gt;
  &lt;link rel="canonical" href="https://www.example.com/page-url/" /&gt;

  &lt;!-- Open Graph --&gt;
  &lt;meta property="og:locale" content="en_US" /&gt;
  &lt;meta property="og:type" content="article" /&gt;
  &lt;meta property="og:title" content="Central Railway Announces 2,012 Summer Special Train Services to Tackle Peak Season Rush - redBus Blog" /&gt;
  &lt;meta property="og:description" content="To tackle surge of demand during summer, Central railways has announced 2012 special trains. Passengers can start booking tickets through redRail to beat the rush." /&gt;
  &lt;meta property="og:url" content="https://www.redbus.in/blog/central-railway-announces-2012-summer-special-train-services-to-tackle-peak-season-rush/" /&gt;
  &lt;meta property="og:site_name" content="redBus Blog" /&gt;
  &lt;meta property="article:published_time" content="2026-03-25T10:27:48+00:00" /&gt;
  &lt;meta property="og:image" content="http://blog.redbus.in/wp-content/uploads/2026/03/Summer-Trains.png" /&gt;
  &lt;meta property="og:image:width" content="640" /&gt;
  &lt;meta property="og:image:height" content="360" /&gt;
  &lt;meta property="og:image:type" content="image/png" /&gt;
  &lt;meta name="author" content="Veda Sree" /&gt;

  &lt;!-- For Twitter --&gt;
  &lt;meta name="twitter:card" content="summary_large_image" /&gt;
  &lt;meta name="twitter:label1" content="Written by" /&gt;
  &lt;meta name="twitter:data1" content="Veda Sree" /&gt;
  &lt;meta name="twitter:label2" content="Est. reading time" /&gt;
  &lt;meta name="twitter:data2" content="2 minutes" /&gt;

  &lt;!-- Schema --&gt;
  &lt;script type="application/ld+json"&gt;
  {
    "@context": "https://schema.org",
    "@type": "WebPage",
    "name": "Example Page",
    "url": "https://example.com/page"
  }
  &lt;/script&gt;

&lt;/head&gt;
</pre>

            </div>
        </div>

    </div>
</div>

<!-- BACK BUTTON -->
<div class="text-center mt-4">
    <a href="{{ url()->previous() }}" class="bpv-back-btn">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

@endsection