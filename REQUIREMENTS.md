# PoemIT! - Software Requirements Specification (SRS)

## Version 1.0 | February 2026

---

## 1. INTRODUCTION

### 1.1 Purpose
This document specifies the functional and non-functional requirements for PoemIT!, a web-based poetry sharing platform designed for the Tribhuvan University BCA 4th Semester project.

### 1.2 Scope
PoemIT! is a PHP-based web application that enables users to create, share, discover, and engage with poetry content. The platform includes user authentication, content moderation, and community engagement features.

### 1.3 Audience
- Project Supervisor and Evaluators
- Development Team
- System Administrators
- End Users (Poets, Readers, Administrators)

---

## 2. FUNCTIONAL REQUIREMENTS

### 2.1 User Management Module

#### FR-1: User Registration
- **Description**: Users can create new accounts on the platform
- **Pre-condition**: User is not logged in
- **Steps**:
  1. User navigates to signup page
  2. Enters username, email, password, and full name
  3. System validates input data
  4. Account is created and user is redirected to login
- **Post-condition**: New user account created in database
- **Validation Rules**:
  - Username: 3-50 characters, alphanumeric
  - Email: Valid email format, unique
  - Password: Minimum 8 characters
  - Full name: Required, 1-100 characters

#### FR-2: User Login
- **Description**: Registered users can authenticate into the system
- **Pre-condition**: User has valid account
- **Steps**:
  1. User enters email and password
  2. System verifies credentials against database
  3. Session is created upon successful authentication
  4. User redirected to home page
- **Post-condition**: User session established
- **Error Handling**:
  - Invalid email: "User not found"
  - Invalid password: "Incorrect password"
  - Multiple failed attempts: Account locked (future enhancement)

#### FR-3: User Logout
- **Description**: Users can end their session
- **Pre-condition**: User is logged in
- **Steps**:
  1. User clicks logout button
  2. Session is destroyed
  3. User redirected to login page
- **Post-condition**: User session terminated

#### FR-4: User Profile Management
- **Description**: Users can view and edit their profile information
- **Pre-condition**: User is logged in
- **Allowed Modifications**:
  - Full name
  - Bio/About section
  - Avatar/Profile picture
  - Email address (with verification)
- **Restrictions**:
  - Cannot modify username
  - Cannot modify registration date
- **Post-condition**: Profile information updated

#### FR-5: Password Reset
- **Description**: Users can reset forgotten passwords
- **Pre-condition**: User is not logged in
- **Steps**:
  1. User enters email address
  2. System sends reset link to email
  3. User clicks link and enters new password
  4. Password is updated and user can login
- **Post-condition**: Password changed in database

### 2.2 Poetry Management Module

#### FR-6: Create Text Poem
- **Description**: Users can create poems in text format
- **Pre-condition**: User is logged in
- **Input Fields**:
  - Title (Required, 1-255 characters)
  - Content (Required, 1-10000 characters)
  - Tags (Optional, comma-separated)
  - Format: Text (Auto-selected)
- **Process**:
  1. User fills form and submits
  2. System validates input
  3. Poem saved with 'pending' status
  4. User receives confirmation
- **Post-condition**: Poem created awaiting moderation

#### FR-7: Create Image Poem
- **Description**: Users can upload poems as images
- **Pre-condition**: User is logged in
- **Constraints**:
  - File types: JPEG, PNG, GIF, WebP
  - Maximum size: 5MB
  - Minimum dimensions: 100x100px
- **Process**:
  1. User selects image file
  2. System validates file
  3. File uploaded to server
  4. Poem created with pending status
- **Post-condition**: Image file stored, poem created

#### FR-8: Create Document Poem
- **Description**: Users can upload poems as documents
- **Pre-condition**: User is logged in
- **Constraints**:
  - File types: PDF, DOC, DOCX
  - Maximum size: 5MB
- **Process**:
  1. User selects document file
  2. System validates file
  3. File uploaded to server
  4. Poem created with pending status
- **Post-condition**: Document stored, poem created

#### FR-9: View Poem Details
- **Description**: Users can view full poem information
- **Pre-condition**: Poem status is 'approved' (or user is author/admin)
- **Display Information**:
  - Poem title and content/file
  - Author information
  - Publication date
  - Like count
  - Comment count
  - View count (incremented)
  - All comments with author info
- **Post-condition**: View count incremented, page displayed

#### FR-10: Edit Poem
- **Description**: Users can modify their own poems
- **Pre-condition**: User is logged in and is poem author
- **Allowed Modifications**:
  - Title
  - Content/File (for text poems)
  - Tags
- **Restrictions**:
  - Cannot change format
  - Cannot change author
  - Cannot modify if poem is approved (to maintain moderation integrity)
- **Post-condition**: Poem updated (status reverted to pending)

#### FR-11: Delete Poem
- **Description**: Users can delete their own poems
- **Pre-condition**: User is logged in and is poem author
- **Process**:
  1. User clicks delete button
  2. Confirmation dialog shown
  3. Poem deleted from database
  4. Associated files deleted
- **Post-condition**: Poem removed, files cleaned up

#### FR-12: Search Poems
- **Description**: Users can search poems by multiple criteria
- **Search Parameters**:
  - Keyword (searches title, content, tags)
  - Author name
  - Date range
  - Format filter
- **Process**:
  1. User enters search query
  2. System queries database using FULLTEXT search
  3. Results displayed in paginated list
  4. Results sortable by relevance, date, likes
- **Post-condition**: Search results displayed

#### FR-13: Browse Poems
- **Description**: Users can browse all approved poems
- **Features**:
  - Chronological listing (newest first)
  - Pagination (20 poems per page)
  - View count display
  - Like count display
  - Author info preview
- **Post-condition**: Poem feed displayed

### 2.3 User Engagement Module

#### FR-14: Like Poem
- **Description**: Users can like poems they enjoy
- **Pre-condition**: User is logged in and poem is approved
- **Process**:
  1. User clicks like button
  2. System checks if already liked
  3. Like record created in database
  4. Like count updated
  5. Button state changes to "liked"
- **Constraints**:
  - User cannot like same poem twice
  - Cannot like own poems
- **Post-condition**: Like recorded, count updated

#### FR-15: Unlike Poem
- **Description**: Users can remove their likes
- **Pre-condition**: User has already liked the poem
- **Process**:
  1. User clicks like button (already liked state)
  2. Like record deleted
  3. Like count decremented
  4. Button state changes to "like"
- **Post-condition**: Like removed

#### FR-16: Comment on Poem
- **Description**: Users can add comments to poems
- **Pre-condition**: User is logged in, poem is approved
- **Input**:
  - Comment text (1-500 characters)
- **Process**:
  1. User enters comment
  2. System validates input
  3. Comment stored in database
  4. Comment displayed immediately
  5. Comment count incremented
- **Post-condition**: Comment created and displayed

#### FR-17: View Comments
- **Description**: Users can view all comments on a poem
- **Display**:
  - Comment text
  - Author name and avatar
  - Publication date
  - Comments sorted chronologically (newest first)
- **Post-condition**: Comments displayed

#### FR-18: Delete Comment
- **Description**: Users can delete their own comments
- **Pre-condition**: User is logged in and is comment author
- **Process**:
  1. User clicks delete on comment
  2. Confirmation shown
  3. Comment deleted
  4. Count decremented
- **Post-condition**: Comment removed

### 2.4 Admin Moderation Module

#### FR-19: View Pending Poems
- **Description**: Admin can view poems awaiting approval
- **Pre-condition**: User is logged in and is admin
- **Display**:
  - List of pending poems
  - Author information
  - Submission date
  - Poem preview
  - Action buttons (Approve/Reject)
- **Post-condition**: Pending poems listed

#### FR-20: Approve Poem
- **Description**: Admin can approve pending poems
- **Pre-condition**: Admin is viewing pending poems
- **Process**:
  1. Admin clicks approve button
  2. Poem status changed to 'approved'
  3. Poem becomes visible to all users
  4. Author notified (future enhancement)
- **Post-condition**: Poem status updated, now public

#### FR-21: Reject Poem
- **Description**: Admin can reject poems for moderation reasons
- **Pre-condition**: Admin is viewing pending poems
- **Input**:
  - Rejection reason (optional)
- **Process**:
  1. Admin clicks reject button
  2. Enters reason (optional)
  3. Poem status changed to 'rejected'
  4. Author notified with reason (future enhancement)
- **Post-condition**: Poem marked rejected

#### FR-22: Admin Dashboard
- **Description**: Admin can view platform statistics and manage content
- **Dashboard Metrics**:
  - Total users
  - Total poems (by status)
  - Total likes and comments
  - Pending reviews count
  - Recent activity
- **Management Functions**:
  - View all poems with status filter
  - View all users
  - Search poems/users
  - Review statistics
- **Post-condition**: Dashboard data displayed

---

## 3. NON-FUNCTIONAL REQUIREMENTS

### 3.1 Performance Requirements

#### NFR-1: Response Time
- **Requirement**: Page load time < 2 seconds for 95% of requests
- **Measurement**: Using standard network conditions
- **Target**: Average response time < 1.5 seconds

#### NFR-2: Database Query Performance
- **Requirement**: Database queries complete in < 500ms
- **Optimization**: Proper indexing on frequently queried columns
- **Monitoring**: Query performance logging

#### NFR-3: Concurrent Users
- **Requirement**: Support minimum 100 concurrent users
- **Scalability**: Can be increased with infrastructure improvements
- **Database Connections**: Connection pooling implemented

#### NFR-4: File Upload Performance
- **Requirement**: File upload < 5 seconds for 5MB file
- **Optimization**: Asynchronous processing (future)
- **Validation**: Pre-upload validation on client

### 3.2 Security Requirements

#### NFR-5: Password Security
- **Requirement**: Passwords hashed using bcrypt algorithm
- **Implementation**: PHP's `password_hash()` function
- **Minimum Strength**: 8 characters minimum
- **No Plain Text**: Passwords never stored or transmitted in plain text

#### NFR-6: SQL Injection Prevention
- **Requirement**: All database queries use parameterized statements
- **Implementation**: PDO prepared statements with bound parameters
- **Code Review**: Security audit of all database operations

#### NFR-7: XSS Prevention
- **Requirement**: All user input escaped in output
- **Implementation**: `htmlspecialchars()` on display
- **Content Types**: Special handling for comments and poems
- **Validation**: Input validation on server-side

#### NFR-8: CSRF Protection
- **Requirement**: Form submissions protected with CSRF tokens
- **Implementation**: Session-based token generation
- **Validation**: Token verified on form processing
- **Scope**: All state-changing operations protected

#### NFR-9: File Upload Security
- **Requirement**: Secure file handling with validation
- **Implementation**:
  - File type verification (MIME type check)
  - File size validation (5MB max)
  - Filename randomization
  - Stored outside web root (future)
  - No direct script execution in upload directory

#### NFR-10: Authentication Security
- **Requirement**: Secure session management
- **Implementation**:
  - HTTPS only (in production)
  - Secure session cookies
  - Session timeout after 24 hours
  - Logout clears session

#### NFR-11: Data Protection
- **Requirement**: Sensitive data protected from unauthorized access
- **Implementation**:
  - Database access control
  - User can only access own data
  - Admin access logging (future)
  - Data encryption in transit (HTTPS)

### 3.3 Usability Requirements

#### NFR-12: User Interface
- **Requirement**: Intuitive interface requiring minimal training
- **Implementation**:
  - Clear navigation
  - Consistent design patterns
  - Helpful error messages
  - Confirmation dialogs for destructive actions

#### NFR-13: Responsive Design
- **Requirement**: Works on mobile, tablet, and desktop
- **Breakpoints**:
  - Mobile: 320px - 767px
  - Tablet: 768px - 1024px
  - Desktop: 1025px and above
- **Testing**: Tested on major browsers and devices

#### NFR-14: Accessibility
- **Requirement**: WCAG 2.1 Level AA compliance (targeted)
- **Implementation**:
  - Semantic HTML
  - ARIA labels
  - Keyboard navigation
  - Color contrast ratios > 4.5:1
  - Screen reader compatible

#### NFR-15: Browser Compatibility
- **Requirement**: Support major modern browsers
- **Browsers**:
  - Chrome 90+
  - Firefox 88+
  - Safari 14+
  - Edge 90+
- **Fallbacks**: Graceful degradation for older browsers

### 3.4 Reliability Requirements

#### NFR-16: Availability
- **Requirement**: System available 99% of operational hours
- **Downtime**: Maximum 2.4 hours per month maintenance
- **Recovery**: Auto-recovery from minor failures

#### NFR-17: Data Integrity
- **Requirement**: Data consistency and integrity maintained
- **Implementation**:
  - Foreign key constraints
  - Transaction support
  - Backup procedures
  - Data validation at all layers

#### NFR-18: Error Handling
- **Requirement**: Graceful error handling without data loss
- **Implementation**:
  - Try-catch blocks for exceptions
  - User-friendly error messages
  - Error logging for debugging
  - Automatic recovery when possible

#### NFR-19: Backup and Recovery
- **Requirement**: Data backup and recovery procedures
- **Implementation**:
  - Daily database backups
  - File backups for uploads
  - Recovery procedures documented
  - Testing of recovery procedures

### 3.5 Maintainability Requirements

#### NFR-20: Code Quality
- **Requirement**: Clean, well-documented code
- **Implementation**:
  - Consistent coding standards
  - Code comments for complex logic
  - Meaningful variable names
  - DRY principle followed

#### NFR-21: Scalability
- **Requirement**: System designed for future growth
- **Implementation**:
  - Modular architecture
  - Database optimization for scale
  - Room for optimization
  - Framework upgrade path

#### NFR-22: Extensibility
- **Requirement**: Easy to add new features
- **Implementation**:
  - Well-structured code
  - Configuration-driven features
  - Plugin architecture (future)
  - API for third-party integration

---

## 4. USE CASES

### Use Case 1: User Registration
**Actor**: New User  
**Precondition**: User has not registered  
**Main Flow**:
1. User navigates to signup page
2. Enters username, email, password
3. Clicks signup button
4. System validates data
5. Account created
6. Redirected to login page

**Alternative Flow**:
- If email exists: Show error, ask for different email

### Use Case 2: Create and Publish Poem
**Actor**: Registered User  
**Precondition**: User is logged in  
**Main Flow**:
1. User clicks "Write" button
2. Selects poem format
3. Enters title and content
4. Adds tags
5. Clicks submit
6. Poem saved with pending status
7. User sees confirmation

**Post-condition**: Poem awaits admin approval

### Use Case 3: Like and Comment on Poem
**Actor**: Registered User  
**Precondition**: User is logged in, poem is approved  
**Main Flow**:
1. User views poem
2. Clicks like button
3. Like count increments
4. Enters comment text
5. Clicks add comment
6. Comment appears below poem

**Post-condition**: Like and comment recorded

### Use Case 4: Moderate Content
**Actor**: Admin User  
**Precondition**: Admin is logged in  
**Main Flow**:
1. Admin navigates to dashboard
2. Views pending poems
3. Reviews poem content
4. Clicks approve or reject
5. Provides feedback if rejecting
6. Poem status updated

**Post-condition**: Poem status changed, visible to users or rejected

---

## 5. DATA DICTIONARY

### Users Table

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| id | INT | PK, AUTO | Unique user identifier |
| username | VARCHAR(50) | UK, NOT NULL | Unique username |
| email | VARCHAR(255) | UK, NOT NULL | User email address |
| password_hash | VARCHAR(255) | NOT NULL | Bcrypt hashed password |
| full_name | VARCHAR(100) | | User's full name |
| bio | TEXT | | User biography |
| is_admin | BOOLEAN | DEFAULT 0 | Admin flag |
| avatar_url | VARCHAR(255) | | Profile picture path |
| created_at | TIMESTAMP | DEFAULT NOW | Account creation time |
| updated_at | TIMESTAMP | DEFAULT NOW | Last update time |

### Poems Table

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| id | INT | PK, AUTO | Unique poem identifier |
| user_id | INT | FK(users), NOT NULL | Author user ID |
| title | VARCHAR(255) | NOT NULL | Poem title |
| content | TEXT | | Poem content (for text format) |
| format | ENUM | DEFAULT 'text' | Format: text/image/document |
| file_url | VARCHAR(500) | | URL to uploaded file |
| tags | VARCHAR(500) | | Comma-separated tags |
| status | ENUM | DEFAULT 'pending' | pending/approved/rejected |
| views_count | INT | DEFAULT 0 | Number of views |
| created_at | TIMESTAMP | DEFAULT NOW | Creation time |
| updated_at | TIMESTAMP | DEFAULT NOW | Last update time |

### Likes Table

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| id | INT | PK, AUTO | Unique like identifier |
| user_id | INT | FK(users), NOT NULL | User who liked |
| poem_id | INT | FK(poems), NOT NULL | Poem liked |
| created_at | TIMESTAMP | DEFAULT NOW | Like time |
| Unique | (user_id, poem_id) | | User can like poem once |

### Comments Table

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| id | INT | PK, AUTO | Unique comment ID |
| user_id | INT | FK(users), NOT NULL | Comment author |
| poem_id | INT | FK(poems), NOT NULL | Commented poem |
| content | TEXT | NOT NULL | Comment text |
| created_at | TIMESTAMP | DEFAULT NOW | Comment time |
| updated_at | TIMESTAMP | DEFAULT NOW | Last edit time |

---

## 6. ACCEPTANCE CRITERIA

### Acceptance Test Checklist

**Authentication Module:**
- [ ] Users can register with valid credentials
- [ ] Duplicate email rejected
- [ ] Users can login with correct credentials
- [ ] Incorrect credentials rejected
- [ ] Users can logout successfully
- [ ] Session destroyed after logout

**Poetry Management:**
- [ ] Users can create text poems
- [ ] Users can upload image poems
- [ ] Users can upload document poems
- [ ] Poems saved with pending status
- [ ] Users can edit own poems
- [ ] Users cannot edit others' poems
- [ ] Users can delete own poems
- [ ] Deleted poems removed from database

**Engagement:**
- [ ] Users can like poems
- [ ] Like count updates correctly
- [ ] Users can unlike poems
- [ ] Users can add comments
- [ ] Comments display on poem page
- [ ] Users can view comment list

**Admin Functions:**
- [ ] Admin can view pending poems
- [ ] Admin can approve poems
- [ ] Approved poems become visible
- [ ] Admin can reject poems
- [ ] Rejected poems marked as rejected

**Search & Discovery:**
- [ ] Search by keyword works
- [ ] Results filtered by approved status
- [ ] Home page shows approved poems
- [ ] User profiles show their poems

**Security:**
- [ ] SQL injection attempts fail
- [ ] XSS attempts prevented
- [ ] CSRF protection working
- [ ] Unauthorized users cannot access admin panel

**Performance:**
- [ ] Page loads in < 2 seconds
- [ ] File uploads complete in < 5 seconds
- [ ] Database queries complete in < 500ms

**Usability:**
- [ ] Interface is intuitive
- [ ] Error messages are clear
- [ ] Navigation is consistent
- [ ] Responsive on mobile devices

---

## 7. CONSTRAINTS

### Technical Constraints
- Vanilla PHP 7.4+ required
- MySQL 5.7+ required
- XAMPP or equivalent environment
- No external frameworks allowed (pure PHP)
- Vanilla JavaScript only (no jQuery or libraries)

### Business Constraints
- Maximum file size: 5MB
- Platform limited to English language
- No payment processing
- No premium features
- Community-driven moderation only

### Timeline Constraints
- Project duration: 12 weeks
- Must be completed for TU BCA evaluation
- Database setup required before frontend development

---

## Version History

| Version | Date | Changes | Author |
|---------|------|---------|--------|
| 1.0 | Feb 2026 | Initial SRS | Project Team |

---

**Document Owner**: Project Supervisor  
**Last Reviewed**: February 2026  
**Next Review**: Upon project completion
