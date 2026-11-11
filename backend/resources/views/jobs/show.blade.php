@extends('layouts.app')

@php
$pageTitle = $job->localized_title . ' - ' . __('Careers');
@endphp

@section('content')
<!-- Page Header -->
<section class="job-detail-header">
    <div class="container">
        <a href="{{ route('jobs.index') }}" class="back-link" data-aos="fade-right">
            <i class="fas fa-arrow-left"></i> {{ __('All Jobs') }}
        </a>
        <h1 data-aos="fade-up">{{ $job->localized_title }}</h1>
        <div class="job-detail-meta" data-aos="fade-up" data-aos-delay="100">
            <span class="job-type job-type-{{ $job->type }}">
                @switch($job->type)
                    @case('full-time') <i class="fas fa-clock"></i> {{ __('Full Time') }} @break
                    @case('part-time') <i class="fas fa-hourglass-half"></i> {{ __('Part Time') }} @break
                    @case('remote') <i class="fas fa-laptop-house"></i> {{ __('Remote') }} @break
                    @case('contract') <i class="fas fa-file-contract"></i> {{ __('Contract') }} @break
                @endswitch
            </span>
            @if($job->department)
            <span><i class="fas fa-building"></i> {{ $job->department }}</span>
            @endif
            @if($job->location)
            <span><i class="fas fa-map-marker-alt"></i> {{ $job->location }}</span>
            @endif
        </div>
    </div>
</section>

<!-- Job Content -->
<section class="job-detail-content">
    <div class="container">
        <div class="job-detail-grid">
            <!-- Main Content -->
            <div class="job-main" data-aos="fade-up">
                <div class="content-card">
                    <h2><i class="fas fa-info-circle"></i> {{ __('Job Description') }}</h2>
                    <div class="rich-text">{!! $job->localized_description !!}</div>
                </div>

                @if($job->localized_requirements)
                <div class="content-card">
                    <h2><i class="fas fa-clipboard-list"></i> {{ __('Requirements') }}</h2>
                    <div class="rich-text">{!! $job->localized_requirements !!}</div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="job-sidebar" data-aos="fade-up" data-aos-delay="100">
                <div class="sidebar-card">
                    <h3>{{ __('Job Summary') }}</h3>
                    <ul class="summary-list">
                        <li>
                            <i class="fas fa-briefcase"></i>
                            <div>
                                <span class="label">{{ __('Type') }}</span>
                                <span class="value">{{ ucfirst(str_replace('-', ' ', $job->type)) }}</span>
                            </div>
                        </li>
                        @if($job->department)
                        <li>
                            <i class="fas fa-building"></i>
                            <div>
                                <span class="label">{{ __('Department') }}</span>
                                <span class="value">{{ $job->department }}</span>
                            </div>
                        </li>
                        @endif
                        @if($job->location)
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <span class="label">{{ __('Location') }}</span>
                                <span class="value">{{ $job->location }}</span>
                            </div>
                        </li>
                        @endif
                        @if($job->deadline)
                        <li>
                            <i class="far fa-calendar-alt"></i>
                            <div>
                                <span class="label">{{ __('Deadline') }}</span>
                                <span class="value {{ $job->deadline->isPast() ? 'text-danger' : '' }}">
                                    {{ $job->deadline->format('M d, Y') }}
                                </span>
                            </div>
                        </li>
                        @endif
                        <li>
                            <i class="far fa-clock"></i>
                            <div>
                                <span class="label">{{ __('Posted') }}</span>
                                <span class="value">{{ $job->created_at->diffForHumans() }}</span>
                            </div>
                        </li>
                    </ul>

                    @if(!$job->deadline || !$job->deadline->isPast())
                    <a href="{{ route('jobs.apply', $job->slug) }}" class="apply-big-btn">
                        <i class="fas fa-paper-plane"></i> {{ __('Apply Now') }}
                    </a>
                    @else
                    <div class="expired-notice">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ __('Application deadline has passed') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
.job-detail-header {
    background: linear-gradient(135deg, var(--primary-blue) 0%, #1e3a8a 100%);
    padding: 2rem 0;
    color: #fff;
}
.back-link {
    color: rgba(255,255,255,0.8);
    text-decoration: none;
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
    transition: color 0.3s;
}
.back-link:hover { color: #fff; }
.job-detail-header h1 { color: #fff; font-size: 1.75rem; margin-bottom: 1rem; }
.job-detail-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    align-items: center;
}
.job-detail-meta span {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    font-size: 0.9rem;
    color: rgba(255,255,255,0.85);
}
.job-detail-content { padding: 2rem 0 3rem; }
.job-detail-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
}
.content-card {
    background: #fff;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    margin-bottom: 1.5rem;
}
.content-card h2 {
    color: var(--primary-blue);
    font-size: 1.2rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #f0f0f0;
}
.content-card h2 i { color: var(--secondary-green); }
.rich-text { font-size: 0.95rem; line-height: 1.8; color: var(--dark-gray); }
.rich-text ul, .rich-text ol { padding-left: 1.5rem; }
.rich-text li { margin-bottom: 0.5rem; }

/* Sidebar */
.sidebar-card {
    background: #fff;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    position: sticky;
    top: 100px;
}
.sidebar-card h3 {
    color: var(--primary-blue);
    font-size: 1.1rem;
    margin-bottom: 1.25rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #f0f0f0;
}
.summary-list { list-style: none; padding: 0; margin: 0 0 1.5rem; }
.summary-list li {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f8f8f8;
}
.summary-list li:last-child { border-bottom: none; }
.summary-list li i { color: var(--primary-blue); font-size: 1rem; margin-top: 0.15rem; width: 20px; text-align: center; }
.summary-list .label { display: block; font-size: 0.75rem; color: var(--medium-gray); text-transform: uppercase; letter-spacing: 0.5px; }
.summary-list .value { display: block; font-size: 0.95rem; color: var(--dark-gray); font-weight: 600; }
.apply-big-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    background: linear-gradient(135deg, var(--primary-blue), var(--secondary-green));
    color: #fff;
    padding: 0.85rem;
    border-radius: 12px;
    font-weight: 700;
    font-size: 1rem;
    text-decoration: none;
    transition: all 0.3s ease;
    width: 100%;
}
.apply-big-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(13,110,253,0.3);
    color: #fff;
}
.expired-notice {
    background: #fef2f2;
    color: #dc2626;
    padding: 0.85rem;
    border-radius: 12px;
    text-align: center;
    font-weight: 600;
    font-size: 0.9rem;
}

@media (min-width: 768px) {
    .job-detail-header { padding: 3rem 0; }
    .job-detail-header h1 { font-size: 2.25rem; }
}
@media (min-width: 1024px) {
    .job-detail-grid { grid-template-columns: 2fr 1fr; }
    .job-detail-content { padding: 3rem 0 4rem; }
}
</style>
@endpush
