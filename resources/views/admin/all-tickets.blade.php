@extends('layouts.admin')

@section('page-title', request('status') === 'verified' ? 'Verified tickets' : 'All tickets')

@section('content')
<div class="admin-ticket-page">
    <div class="row">
        <div class="col-12">
            <h1 class="admin-page-title">{{ request('status') === 'verified' ? 'Verified Tickets' : 'All Tickets' }}</h1><p class="admin-page-subtitle mb-3">Search, filter, and manage every ticket in one place.</p>
            <a href="{{ route('admin.dashboard') }}" class="custom-btn-admin secondary mb-3"><i class="bi bi-arrow-left" aria-hidden="true"></i> Back to Dashboard</a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            
            <div class="card admin-section-card">
                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="filter-section">
                        <form id="search-form" method="GET" action="{{ route('admin.tickets.index') }}" class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label for="email" class="form-label">Search by Email</label>
                                <input type="text" class="form-control" id="email" name="email" 
                                    placeholder="Enter email address" value="{{ request('email') }}" autocomplete="off">
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Filter by Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">All Statuses</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    <option value="checked_in" {{ request('status') == 'checked_in' ? 'selected' : '' }}>Checked In</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="custom-btn-admin w-100" id="search-btn">
                                    <span id="search-btn-text">Search/Filter</span>
                                    <span id="search-btn-spinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                                </button>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('admin.tickets.index') }}" class="custom-btn-admin secondary w-100 d-block text-center" id="clear-btn">Clear All</a>
                            </div>
                        </form>
                    </div>
                    
                    <div id="filters-info" class="mt-3 mb-3" style="display: none;">
                        <div class="alert alert-info">
                            <strong>Active Filters:</strong>
                            <span id="email-filter" class="badge bg-primary me-2" style="display: none;"></span>
                            <span id="status-filter" class="badge bg-secondary" style="display: none;"></span>
                        </div>
                    </div>
                    
                    <div id="tickets-container">
                        @include('admin.partials.tickets-table', ['tickets' => $tickets])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('search-form');
    const ticketsContainer = document.getElementById('tickets-container');
    const searchBtn = document.getElementById('search-btn');
    const searchBtnText = document.getElementById('search-btn-text');
    const searchBtnSpinner = document.getElementById('search-btn-spinner');
    const filtersInfo = document.getElementById('filters-info');
    const emailFilter = document.getElementById('email-filter');
    const statusFilter = document.getElementById('status-filter');
    const clearBtn = document.getElementById('clear-btn');
    
    // Update filters info on page load
    updateFiltersInfo();
    
    // Handle form submission with AJAX
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        performSearch();
    });
    
    // Handle clear button
    clearBtn.addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('email').value = '';
        document.getElementById('status').value = '';
        performSearch();
    });
    
    // Handle pagination links (delegate event)
    ticketsContainer.addEventListener('click', function(e) {
        if (e.target.closest('.pagination a')) {
            e.preventDefault();
            const url = e.target.closest('a').href;
            performSearch(url);
        }
    });
    
    function performSearch(url = null) {
        // Show loading state
        searchBtn.disabled = true;
        searchBtnText.style.display = 'none';
        searchBtnSpinner.style.display = 'inline-block';
        
        // Get form data
        const formData = new FormData(searchForm);
        const params = new URLSearchParams();
        
        for (const [key, value] of formData.entries()) {
            if (value) {
                params.append(key, value);
            }
        }
        
        // Use provided URL or construct from form action
        const searchUrl = url || searchForm.action + '?' + params.toString();
        
        // Make AJAX request
        fetch(searchUrl, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            // Update tickets container
            document.querySelectorAll('body > .modal[data-ticket-dialog]').forEach(modal => {
                bootstrap.Modal.getInstance(modal)?.dispose();
                modal.remove();
            });
            ticketsContainer.innerHTML = data.html;
            window.prepareAdminDialogs?.();
            
            // Update filters info
            updateFiltersInfo(data.filters);
            
            // Update URL without page reload
            const newUrl = new URL(searchUrl, window.location.origin);
            window.history.pushState({}, '', newUrl.pathname + newUrl.search);
            
            // Scroll to top of container
            ticketsContainer.scrollIntoView({ behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth', block: 'start' });
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while searching. Please try again.');
        })
        .finally(() => {
            // Hide loading state
            searchBtn.disabled = false;
            searchBtnText.style.display = 'inline';
            searchBtnSpinner.style.display = 'none';
        });
    }
    
    function updateFiltersInfo(filters = null) {
        const email = filters ? filters.email : document.getElementById('email').value;
        const status = filters ? filters.status : document.getElementById('status').value;
        
        if (email || status) {
            filtersInfo.style.display = 'block';
            
            if (email) {
                emailFilter.textContent = 'Email: ' + email;
                emailFilter.style.display = 'inline-block';
            } else {
                emailFilter.style.display = 'none';
            }
            
            if (status) {
                const statusText = status.charAt(0).toUpperCase() + status.slice(1).replace('_', ' ');
                statusFilter.textContent = 'Status: ' + statusText;
                statusFilter.style.display = 'inline-block';
            } else {
                statusFilter.style.display = 'none';
            }
        } else {
            filtersInfo.style.display = 'none';
        }
    }
    
    // Handle browser back/forward buttons
    window.addEventListener('popstate', function(e) {
        location.reload();
    });
});
</script>
@endsection

