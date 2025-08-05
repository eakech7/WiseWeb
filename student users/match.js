// Sample match data
const matchesData = [
  {
    id: 1,
    name: "Stacey Wambui",
    course: "Computer Science",
    compatibility: 95,
    avatar: "SW",
    details: {
      year: "3rd Year",
      availability: "Mon-Wed 2-5 PM",
      location: "Library Study Room",
      studyStyle: "Group Discussion"
    },
    tags: ["Same Course", "Similar Schedule", "High GPA"],
    commonInterests: ["Programming", "Data Structures and Algorithms", "Database Systems"]
  },
  {
    id: 2,
    name: "John Mumo",
    course: "Engineering",
    compatibility: 87,
    avatar: "JM",
    details: {
      year: "2nd Year",
      availability: "Tue-Thu 3-6 PM",
      location: "Science Building",
      studyStyle: "Collaborative"
    },
    tags: ["Overlapping Time", "Lab Partner", "Same Building"],
    commonInterests: ["Mechanics", "Thermodynamics"]
  },
  {
    id: 3,
    name: "Amina Hassan",
    course: "Computer Science",
    compatibility: 92,
    avatar: "AH",
    details: {
      year: "4th Year",
      availability: "Mon-Fri 1-4 PM",
      location: "CS Lab",
      studyStyle: "Pair Programming"
    },
    tags: ["Same Major", "Senior Student", "Mentor Available"],
    commonInterests: ["Algorithms", "Web Development", "AI"]
  },
  {
    id: 4,
    name: "David Rodriguez",
    course: "Chemistry",
    compatibility: 78,
    avatar: "DR",
    details: {
      year: "3rd Year",
      availability: "Weekend 10 AM-2 PM",
      location: "Chemistry Lab",
      studyStyle: "Hands-on Practice"
    },
    tags: ["Weekend Available", "Lab Experience", "Study Groups"],
    commonInterests: ["Organic Chemistry", "Research"]
  },
  {
    id: 5,
    name: "Sarah Williams",
    course: "Biology",
    compatibility: 89,
    avatar: "SW",
    details: {
      year: "2nd Year",
      availability: "Daily 4-7 PM",
      location: "Biology Building",
      studyStyle: "Visual Learning"
    },
    tags: ["Daily Availability", "Visual Learner", "Note Sharing"],
    commonInterests: ["Molecular Biology", "Genetics"]
  },
  {
    id: 6,
    name: "Michael Chang",
    course: "English Literature",
    compatibility: 83,
    avatar: "MC",
    details: {
      year: "1st Year",
      availability: "Evening 6-9 PM",
      location: "Literature Lounge",
      studyStyle: "Discussion & Analysis"
    },
    tags: ["Evening Study", "Discussion Focused", "Creative Writing"],
    commonInterests: ["Shakespeare", "Modern Poetry", "Creative Writing"]
  }
];

let currentMatches = [...matchesData];

function renderMatches(matches) {
  const container = document.getElementById('matches-container');
  
  if (matches.length === 0) {
    container.innerHTML = 
      <div style="grid-column: 1 / -1; text-align: center; color: white; padding: 40px;">
        <h3>No matches found</h3>
        <p>Try adjusting your filters to find more study buddies</p>
      </div>
    ;
    return;
  }

  container.innerHTML = matches.map(match => 
    <div class="match-card">
      <div class="match-header">
        <div class="avatar">${match.avatar}</div>
        <div class="match-info">
          <h3>${match.name}</h3>
          <div class="compatibility">
            <div class="compatibility-score">${match.compatibility}% Match</div>
          </div>
        </div>
      </div>
      
      <div class="match-details">
        <div class="detail-item">
          <span class="detail-icon">📚</span>
          <span><strong>Course:</strong> ${match.course}</span>
        </div>
        <div class="detail-item">
          <span class="detail-icon">🎓</span>
          <span><strong>Year:</strong> ${match.details.year}</span>
        </div>
        <div class="detail-item">
          <span class="detail-icon">⏰</span>
          <span><strong>Available:</strong> ${match.details.availability}</span>
        </div>
        <div class="detail-item">
          <span class="detail-icon">📍</span>
          <span><strong>Location:</strong> ${match.details.location}</span>
        </div>
        <div class="detail-item">
          <span class="detail-icon">🤝</span>
          <span><strong>Style:</strong> ${match.details.studyStyle}</span>
        </div>
      </div>
      
      <div class="tags">
        ${match.tags.map(tag => <span class="tag">${tag}</span>).join('')}
      </div>
      
      <div class="match-actions">
        <button class="btn btn-primary" onclick="connectWith(${match.id})">Connect</button>
        <button class="btn btn-secondary" onclick="viewProfile(${match.id})">View Profile</button>
      </div>
    </div>
  ).join('');
}

function filterMatches() {
  const course = document.getElementById('course').value.toLowerCase();
  const availability = document.getElementById('availability').value.toLowerCase();
  const studyStyle = document.getElementById('study-style').value.toLowerCase();
  
  // Show loading
  document.getElementById('loading').style.display = 'block';
  document.getElementById('matches-container').style.display = 'none';
  
  setTimeout(() => {
    let filtered = matchesData.filter(match => {
      const courseMatch = !course || match.course.toLowerCase().includes(course);
      const availabilityMatch = !availability || 
        match.details.availability.toLowerCase().includes(availability) ||
        (availability === 'weekend' && match.details.availability.toLowerCase().includes('weekend'));
      const styleMatch = !studyStyle || 
        match.details.studyStyle.toLowerCase().includes(studyStyle);
      
      return courseMatch && availabilityMatch && styleMatch;
    });
    
    // Sort by compatibility score
    filtered.sort((a, b) => b.compatibility - a.compatibility);
    
    currentMatches = filtered;
    renderMatches(currentMatches);
    
    // Hide loading
    document.getElementById('loading').style.display = 'none';
    document.getElementById('matches-container').style.display = 'grid';
  }, 1000);
}

function connectWith(matchId) {
  const match = matchesData.find(m => m.id === matchId);
  alert(Connection request sent to ${match.name}! They will be notified and can accept your request.);
}

function viewProfile(matchId) {
  const match = matchesData.find(m => m.id === matchId);
  alert(Viewing ${match.name}'s profile...\n\nCommon Interests: ${match.commonInterests.join(', ')}\n\nClick 'Connect' to send a study buddy request!);
}

// Initial render when page loads
document.addEventListener('DOMContentLoaded', function() {
  renderMatches(currentMatches);
});