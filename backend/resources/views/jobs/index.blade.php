@extends('layouts.app')

@php
$pageTitle = __('Careers');
$pageDescription = app()->getLocale() == 'ar' ? 'انضم لفريقنا - تصفح الوظائف المتاحة' : 'Join our team - Browse available positions';
@endphp

@section('content')
<!-- Page Header -->
<section class="jobs-header">
    <div class="container">
        <h1 data-aos="fade-up">
            <i class="fas fa-briefcase"></i>
            {{ __('Careers') }}
        </h1>
        <p data-aos="fade-up" data-aos-delay="100">{{ __('Join Our Team & Make a Difference') }}</p>
    </div>
</section>

<!-- Jobs List -->
<section class="jobs-list-section">
    <div class="container">
        @if($jobs->count() > 0)
        <div class="jobs-grid">
            @foreach($jobs as $index => $job)
            <div class="job-card" data-aos="fade-up" data-aos-delay="{{ min(($index + 1) * 50, 200) }}">
                <div class="job-card-header">
                    <span class="job-type job-type-{{ $job->type }}">
                        @switch($job->type)
                            @case('full-time') <i class="fas fa-clock"></i> {{ __('Full Time') }} @break
                            @case('part-time') <i class="fas fa-hourglass-half"></i> {{ __('Part Time') }} @break
                            @case('remote') <i class="fas fa-laptop-house"></i> {{ __('Remote') }} @break
                            @case('contract') <i class="fas fa-file-contract"></i> {{ __('Contract') }} @break
                        @endswitch
                    </span>
                    @if($job->deadline)
                    <span class="job-deadline">
                        <i class="far fa-calendar-alt"></i>
                        {{ $job->deadline->format('M d, Y') }}
                    </span>
                    @endif
                </div>

                <h3 class="job-title">{{ $job->localized_title }}</h3>

                <div class="job-meta">
                    @if($job->department)
                    <span><i class="fas fa-building"></i> {{ $job->department }}</span>
                    @endif
                    @if($job->location)
                    <span><i class="fas fa-map-marker-alt"></i> {{ $job->location }}</span>
                    @endif
                </div>

                <p class="job-description">{{ Str::limit(strip_tags($job->localized_description), 120) }}</p>

                <a href="{{ route('jobs.show', $job->slug) }}" class="job-apply-btn">
                    {{ __('View Details') }} <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            @endforeach
        </div>

        <div class="jobs-pagination" data-aos="fade-up">
            {{ $jobs->links() }}
        </div>
        @else
        <div class="empty-jobs" data-aos="fade-up">
            <i class="fas fa-briefcase"></i>
            <h3>{{ __('No Open Positions') }}</h3>
            <p>{{ __('Check back soon for new opportunities!') }}</p>
            <a href="{{ route('home') }}" class="btn-back-home">
                <i class="fas fa-home"></i> {{ __('Back to Home') }}
            </a>
        </div>
        @endif
    </div>
</section>
@endsection

@push('styles')
<style>
/* Jobs Header */
.jobs-header {
    background: linear-gradient(135deg, var(--primary-blue) 0%, #1e3a8a 100%);
    padding: 2rem 0;
    text-align: center;
    color: #fff;
}
.jobs-header h1 {
    color: #fff;
    font-size: 1.75rem;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}
.jobs-header h1 i { color: var(--secondary-green); }
.jobs-header p { opacity: 0.9; font-size: 0.95rem; margin: 0; }

/* Jobs Grid */
.jobs-list-section { padding: 2rem 0 3rem; }
.jobs-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.25rem;
}
.job-card {
    background: #fff;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border-left: 4px solid var(--primary-blue);
}
.job-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}
.job-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    flex-wrap: wrap;
    gap: 0.5rem;
}
.job-type {
    padding: 0.35rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
}
.job-type-full-time { background: #dcfce7; color: #166534; }
.job-type-part-time { background: #fef3c7; color: #92400e; }
.job-type-remote { background: #dbeafe; color: #1e40af; }
.job-type-contract { background: #f3e8ff; color: #6b21a8; }
.job-deadline {
    font-size: 0.8rem;
    color: var(--medium-gray);
    display: flex;
    align-items: center;
    gap: 0.3rem;
}
.job-title {
    font-size: 1.2rem;
    color: var(--primary-blue);
    margin-bottom: 0.75rem;
    line-height: 1.4;
}
.job-meta {
    display: flex;
    gap: 1rem;
    margin-bottom: 0.75rem;
    flex-wrap: wrap;
}
.job-meta span {
    font-size: 0.85rem;
    color: var(--medium-gray);
    display: flex;
    align-items: center;
    gap: 0.3rem;
}
.job-meta i { color: var(--secondary-green); }
.job-description {
    font-size: 0.9rem;
    color: var(--dark-gray);
    line-height: 1.6;
    margin-bottom: 1.25rem;
}
.job-apply-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: linear-gradient(135deg, var(--primary-blue), var(--secondary-green));
    color: #fff;
    padding: 0.65rem 1.5rem;
    border-radius: 25px;
    font-weight: 600;
    font-size: 0.9rem;
    text-decoration: none;
    transition: all 0.3s ease;
}
.job-apply-btn:hover {
    transform: translateX(4px);
    box-shadow: 0 4px 15px rgba(13,110,253,0.3);
    color: #fff;
}

/* Empty State */
.empty-jobs {
    text-align: center;
    padding: 4rem 1.5rem;
    background: #f8fafc;
    border-radius: 16px;
}
.empty-jobs i { font-size: 3rem; color: var(--medium-gray); margin-bottom: 1rem; }
.empty-jobs h3 { color: var(--primary-blue); margin-bottom: 0.5rem; }
.empty-jobs p { color: var(--medium-gray); margin-bottom: 1.5rem; }

/* Pagination */
.jobs-pagination { margin-top: 2rem; display: flex; justify-content: center; }

@media (min-width: 768px) {
    .jobs-header { padding: 3rem 0; }
    .jobs-header h1 { font-size: 2.5rem; }
    .jobs-grid { grid-template-columns: repeat(2, 1fr); gap: 1.5rem; }
}
@media (min-width: 1024px) {
    .jobs-grid { grid-template-columns: repeat(3, 1fr); }
    .jobs-list-section { padding: 3rem 0 4rem; }
}
</style>
@endpush
