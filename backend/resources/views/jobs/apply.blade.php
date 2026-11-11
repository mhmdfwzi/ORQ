@extends('layouts.app')

@php
$pageTitle = __('Apply') . ' - ' . $job->localized_title;
@endphp

@section('content')
<!-- Page Header -->
<section class="apply-header">
    <div class="container">
        <a href="{{ route('jobs.show', $job->slug) }}" class="back-link" data-aos="fade-right">
            <i class="fas fa-arrow-left"></i> {{ __('Back to Job') }}
        </a>
        <h1 data-aos="fade-up">{{ __('Apply for') }}: {{ $job->localized_title }}</h1>
    </div>
</section>

<!-- Application Form -->
<section class="apply-form-section">
    <div class="container">
        @if($errors->any())
        <div class="alert-error" data-aos="fade-up">
            <i class="fas fa-exclamation-triangle"></i>
            <div>
                <strong>{{ __('Please fix the following errors:') }}</strong>
                <ul>
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <form action="{{ route('jobs.submit', $job->slug) }}" method="POST" enctype="multipart/form-data" class="application-form" data-aos="fade-up">
            @csrf

            <!-- Personal Information -->
            <div class="form-section">
                <h2><i class="fas fa-user"></i> {{ __('Personal Information') }}</h2>
                <div class="form-grid">
                    <div class="form-group required">
                        <label for="full_name">{{ __('Full Name') }} *</label>
                        <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required placeholder="{{ __('Enter your full name') }}">
                    </div>
                    <div class="form-group">
                        <label for="date_of_birth">{{ __('Date of Birth') }}</label>
                        <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}">
                    </div>
                    <div class="form-group">
                        <label for="gender">{{ __('Gender') }}</label>
                        <select id="gender" name="gender">
                            <option value="">{{ __('Select') }}</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>{{ __('Male') }}</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>{{ __('Female') }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="city">{{ __('City') }}</label>
                        <input type="text" id="city" name="city" value="{{ old('city') }}" placeholder="{{ __('Your city') }}">
                    </div>
                    <div class="form-group full-width">
                        <label for="address">{{ __('Address') }}</label>
                        <input type="text" id="address" name="address" value="{{ old('address') }}" placeholder="{{ __('Your full address') }}">
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="form-section">
                <h2><i class="fas fa-address-book"></i> {{ __('Contact Information') }}</h2>
                <div class="form-grid">
                    <div class="form-group required">
                        <label for="email">{{ __('Email') }} *</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="example@email.com">
                    </div>
                    <div class="form-group required">
                        <label for="phone">{{ __('Phone Number') }} *</label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required placeholder="01XXXXXXXXX">
                    </div>
                </div>
            </div>

            <!-- Education & Training -->
            <div class="form-section">
                <h2><i class="fas fa-graduation-cap"></i> {{ __('Education & Training') }}</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="education_level">{{ __('Education Level') }}</label>
                        <select id="education_level" name="education_level">
                            <option value="">{{ __('Select') }}</option>
                            <option value="High School" {{ old('education_level') == 'High School' ? 'selected' : '' }}>{{ __('High School') }}</option>
                            <option value="Diploma" {{ old('education_level') == 'Diploma' ? 'selected' : '' }}>{{ __('Diploma') }}</option>
                            <option value="Bachelor" {{ old('education_level') == 'Bachelor' ? 'selected' : '' }}>{{ __("Bachelor's Degree") }}</option>
                            <option value="Master" {{ old('education_level') == 'Master' ? 'selected' : '' }}>{{ __("Master's Degree") }}</option>
                            <option value="PhD" {{ old('education_level') == 'PhD' ? 'selected' : '' }}>{{ __('PhD') }}</option>
                            <option value="Other" {{ old('education_level') == 'Other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="university">{{ __('University / Institute') }}</label>
                        <input type="text" id="university" name="university" value="{{ old('university') }}" placeholder="{{ __('Name of institution') }}">
                    </div>
                    <div class="form-group">
                        <label for="major">{{ __('Major / Specialization') }}</label>
                        <input type="text" id="major" name="major" value="{{ old('major') }}" placeholder="{{ __('Your field of study') }}">
                    </div>
                    <div class="form-group">
                        <label for="graduation_year">{{ __('Graduation Year') }}</label>
                        <input type="text" id="graduation_year" name="graduation_year" value="{{ old('graduation_year') }}" placeholder="2024">
                    </div>
                    <div class="form-group full-width">
                        <label for="certifications">{{ __('Certifications & Training') }}</label>
                        <textarea id="certifications" name="certifications" rows="3" placeholder="{{ __('List any relevant certifications, courses, or training programs') }}">{{ old('certifications') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Experience -->
            <div class="form-section">
                <h2><i class="fas fa-briefcase"></i> {{ __('Work Experience') }}</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="years_of_experience">{{ __('Years of Experience') }}</label>
                        <input type="number" id="years_of_experience" name="years_of_experience" value="{{ old('years_of_experience') }}" min="0" max="50" placeholder="0">
                    </div>
                    <div class="form-group">
                        <label for="current_job">{{ __('Current Job Title') }}</label>
                        <input type="text" id="current_job" name="current_job" value="{{ old('current_job') }}" placeholder="{{ __('Your current position') }}">
                    </div>
                    <div class="form-group full-width">
                        <label for="previous_experience">{{ __('Previous Experience') }}</label>
                        <textarea id="previous_experience" name="previous_experience" rows="4" placeholder="{{ __('Describe your previous work experience, companies, and responsibilities') }}">{{ old('previous_experience') }}</textarea>
                    </div>
                    <div class="form-group full-width">
                        <label for="skills">{{ __('Skills') }}</label>
                        <textarea id="skills" name="skills" rows="3" placeholder="{{ __('List your key skills (e.g., Communication, MS Office, Customer Service...)') }}">{{ old('skills') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Cover Letter & CV -->
            <div class="form-section">
                <h2><i class="fas fa-file-alt"></i> {{ __('Cover Letter & CV') }}</h2>
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label for="cover_letter">{{ __('Cover Letter') }}</label>
                        <textarea id="cover_letter" name="cover_letter" rows="5" placeholder="{{ __('Tell us why you are the right fit for this position...') }}">{{ old('cover_letter') }}</textarea>
                    </div>
                    <div class="form-group full-width">
                        <label for="cv_file">{{ __('Upload CV') }} <small>(PDF, DOC, DOCX - {{ __('Max 5MB') }})</small></label>
                        <div class="file-upload-area">
                            <input type="file" id="cv_file" name="cv_file" accept=".pdf,.doc,.docx">
                            <div class="file-upload-label">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span>{{ __('Choose file or drag here') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="form-submit">
                <button type="submit" class="submit-btn">
                    <i class="fas fa-paper-plane"></i> {{ __('Submit Application') }}
                </button>
            </div>
        </form>
    </div>
</section>
@endsection

@push('styles')
<style>
.apply-header {
    background: linear-gradient(135deg, var(--primary-blue) 0%, #1e3a8a 100%);
    padding: 2rem 0;
    color: #fff;
}
.apply-header h1 { color: #fff; font-size: 1.3rem; margin: 0; }
.back-link {
    color: rgba(255,255,255,0.8);
    text-decoration: none;
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
}
.back-link:hover { color: #fff; }

.apply-form-section { padding: 2rem 0 3rem; }
.alert-error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 12px;
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
    display: flex;
    gap: 0.75rem;
    align-items: flex-start;
    color: #dc2626;
}
.alert-error i { font-size: 1.2rem; margin-top: 0.15rem; }
.alert-error ul { margin: 0.5rem 0 0; padding-left: 1.25rem; font-size: 0.9rem; }

.application-form { max-width: 800px; margin: 0 auto; }
.form-section {
    background: #fff;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    margin-bottom: 1.5rem;
}
.form-section h2 {
    color: var(--primary-blue);
    font-size: 1.1rem;
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #f0f0f0;
}
.form-section h2 i { color: var(--secondary-green); }
.form-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
}
.form-group label {
    display: block;
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--dark-gray);
    margin-bottom: 0.4rem;
}
.form-group label small { color: var(--medium-gray); font-weight: 400; }
.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.7rem 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 0.95rem;
    transition: all 0.3s;
    background: #fafafa;
    font-family: inherit;
}
.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: var(--primary-blue);
    outline: none;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(13,110,253,0.1);
}

/* File Upload */
.file-upload-area { position: relative; }
.file-upload-area input[type="file"] {
    position: absolute;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
    z-index: 2;
}
.file-upload-label {
    border: 2px dashed #d1d5db;
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s;
    background: #fafafa;
}
.file-upload-label i { font-size: 2rem; color: var(--primary-blue); margin-bottom: 0.5rem; display: block; }
.file-upload-label span { color: var(--medium-gray); font-size: 0.9rem; }
.file-upload-area:hover .file-upload-label {
    border-color: var(--primary-blue);
    background: #f0f7ff;
}

/* Submit */
.form-submit { text-align: center; margin-top: 1rem; }
.submit-btn {
    background: linear-gradient(135deg, var(--primary-blue), var(--secondary-green));
    color: #fff;
    border: none;
    padding: 0.85rem 2.5rem;
    border-radius: 30px;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s;
}
.submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(13,110,253,0.3);
}

@media (min-width: 768px) {
    .apply-header h1 { font-size: 1.75rem; }
    .form-grid { grid-template-columns: repeat(2, 1fr); }
    .form-group.full-width { grid-column: span 2; }
}
</style>
@endpush
