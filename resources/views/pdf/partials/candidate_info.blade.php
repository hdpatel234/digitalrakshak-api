<div class="info-box clearfix">
    <div class="profile-img-col">
        <div class="profile-img">
            <svg viewBox="0 0 100 100" style="width:100%; height:100%; fill:#e0e0e0;"><path d="M50,10 A20,20 0 1,0 70,30 A20,20 0 0,0 50,10 Z M50,55 C25,55 10,75 10,95 L90,95 C90,75 75,55 50,55 Z"/></svg>
        </div>
        <div class="profile-name">{{ $candidate->first_name }} {{ $candidate->last_name }}</div>
    </div>
    
    <div class="details-col">
        <div class="detail-row">
            <div class="detail-item">
                <div class="detail-label">Staff ID</div>
                <div class="detail-value">{{ $candidate->id ? 'ID-' . $candidate->id : 'N/A' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Mobile</div>
                <div class="detail-value-transparent">{{ $candidate->phone ?? 'N/A' }}</div>
            </div>
        </div>
        
        <div class="detail-row">
            <div class="detail-item">
                <div class="detail-label">Current Address</div>
                <div class="detail-value">
                    {{ $candidate->address ?? 'N/A' }} 
                    {{ $candidate->city ?? '' }} 
                    {{ $candidate->state ?? '' }} 
                    {{ $candidate->pincode ?? '' }}
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Permanent Address</div>
                <div class="detail-value">
                    {{ $candidate->address ?? 'N/A' }} 
                    {{ $candidate->city ?? '' }} 
                    {{ $candidate->state ?? '' }} 
                    {{ $candidate->pincode ?? '' }}
                </div>
            </div>
        </div>
    </div>
</div>
