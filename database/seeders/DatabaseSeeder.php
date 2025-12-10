<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Department;
use App\Models\Program;
use App\Models\Course;
use App\Models\Semester;
use App\Models\CourseSection;
use App\Models\Announcement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Departments
        $cce = Department::create([
            'name' => 'College of Computer Education',
            'code' => 'CCE',
            'description' => 'Department focused on computer science and information technology programs',
        ]);

        $cba = Department::create([
            'name' => 'College of Business Administration',
            'code' => 'CBA',
            'description' => 'Department focused on business and management programs',
        ]);

        // Create Programs
        $bsit = Program::create([
            'department_id' => $cce->id,
            'name' => 'Bachelor of Science in Information Technology',
            'code' => 'BSIT',
            'min_units' => 18,
            'max_units' => 24,
            'description' => 'Four-year program in Information Technology',
        ]);

        $bscs = Program::create([
            'department_id' => $cce->id,
            'name' => 'Bachelor of Science in Computer Science',
            'code' => 'BSCS',
            'min_units' => 18,
            'max_units' => 24,
            'description' => 'Four-year program in Computer Science',
        ]);

        $bsba = Program::create([
            'department_id' => $cba->id,
            'name' => 'Bachelor of Science in Business Administration',
            'code' => 'BSBA',
            'min_units' => 15,
            'max_units' => 21,
            'description' => 'Four-year program in Business Administration',
        ]);

        // Create General Education Courses
        $genEdCourses = [
            ['course_code' => 'GE101', 'name' => 'Mathematics in the Modern World', 'units' => 3, 'is_general' => true],
            ['course_code' => 'GE102', 'name' => 'Purposive Communication', 'units' => 3, 'is_general' => true],
            ['course_code' => 'GE103', 'name' => 'Understanding the Self', 'units' => 3, 'is_general' => true],
            ['course_code' => 'GE104', 'name' => 'Readings in Philippine History', 'units' => 3, 'is_general' => true],
            ['course_code' => 'GE105', 'name' => 'The Contemporary World', 'units' => 3, 'is_general' => true],
            ['course_code' => 'PE101', 'name' => 'Physical Education 1', 'units' => 2, 'is_general' => true],
            ['course_code' => 'NSTP101', 'name' => 'National Service Training Program 1', 'units' => 3, 'is_general' => true],
        ];

        foreach ($genEdCourses as $courseData) {
            Course::create($courseData);
        }

        // Create BSIT Major Courses - ADDED MORE
        $bsitCourses = [
            ['course_code' => 'IT101', 'name' => 'Introduction to Computing', 'units' => 3, 'is_general' => false],
            ['course_code' => 'IT102', 'name' => 'Computer Programming 1', 'units' => 3, 'is_general' => false],
            ['course_code' => 'IT103', 'name' => 'Computer Programming 2', 'units' => 3, 'is_general' => false],
            ['course_code' => 'IT201', 'name' => 'Data Structures and Algorithms', 'units' => 3, 'is_general' => false],
            ['course_code' => 'IT202', 'name' => 'Web Development', 'units' => 3, 'is_general' => false],
            ['course_code' => 'IT203', 'name' => 'Database Management Systems', 'units' => 3, 'is_general' => false],
            ['course_code' => 'IT204', 'name' => 'Network Fundamentals', 'units' => 3, 'is_general' => false],
            ['course_code' => 'IT205', 'name' => 'Object-Oriented Programming', 'units' => 3, 'is_general' => false],
            ['course_code' => 'IT301', 'name' => 'Systems Analysis and Design', 'units' => 3, 'is_general' => false],
            ['course_code' => 'IT302', 'name' => 'Information Assurance and Security', 'units' => 3, 'is_general' => false],
            ['course_code' => 'IT303', 'name' => 'Mobile Application Development', 'units' => 3, 'is_general' => false],
            ['course_code' => 'IT304', 'name' => 'Web Application Security', 'units' => 3, 'is_general' => false],
            ['course_code' => 'IT401', 'name' => 'IT Project Management', 'units' => 3, 'is_general' => false],
            ['course_code' => 'IT402', 'name' => 'Software Engineering', 'units' => 3, 'is_general' => false],
            ['course_code' => 'IT403', 'name' => 'Cloud Computing', 'units' => 3, 'is_general' => false],
        ];

        foreach ($bsitCourses as $courseData) {
            $course = Course::create($courseData);
            $course->programs()->attach($bsit->id);
        }

        // Create BSCS Major Courses
        $bscsCourses = [
            ['course_code' => 'CS101', 'name' => 'Introduction to Computer Science', 'units' => 3, 'is_general' => false],
            ['course_code' => 'CS102', 'name' => 'Discrete Mathematics', 'units' => 3, 'is_general' => false],
            ['course_code' => 'CS201', 'name' => 'Object-Oriented Programming', 'units' => 3, 'is_general' => false],
            ['course_code' => 'CS202', 'name' => 'Computer Architecture', 'units' => 3, 'is_general' => false],
        ];

        foreach ($bscsCourses as $courseData) {
            $course = Course::create($courseData);
            $course->programs()->attach($bscs->id);
        }

        // Create BSBA Major Courses - ADDED MORE
        $bsbaCourses = [
            ['course_code' => 'BA101', 'name' => 'Fundamentals of Accounting', 'units' => 3, 'is_general' => false],
            ['course_code' => 'BA102', 'name' => 'Principles of Management', 'units' => 3, 'is_general' => false],
            ['course_code' => 'BA103', 'name' => 'Business Mathematics', 'units' => 3, 'is_general' => false],
            ['course_code' => 'BA104', 'name' => 'Business Ethics', 'units' => 3, 'is_general' => false],
            ['course_code' => 'BA105', 'name' => 'Principles of Marketing', 'units' => 3, 'is_general' => false],
            ['course_code' => 'BA201', 'name' => 'Marketing Management', 'units' => 3, 'is_general' => false],
            ['course_code' => 'BA202', 'name' => 'Financial Management', 'units' => 3, 'is_general' => false],
            ['course_code' => 'BA203', 'name' => 'Human Resource Management', 'units' => 3, 'is_general' => false],
            ['course_code' => 'BA204', 'name' => 'Operations Management', 'units' => 3, 'is_general' => false],
            ['course_code' => 'BA301', 'name' => 'Strategic Management', 'units' => 3, 'is_general' => false],
            ['course_code' => 'BA302', 'name' => 'Entrepreneurship', 'units' => 3, 'is_general' => false],
            ['course_code' => 'BA303', 'name' => 'International Business', 'units' => 3, 'is_general' => false],
            ['course_code' => 'BA401', 'name' => 'Business Research', 'units' => 3, 'is_general' => false],
            ['course_code' => 'BA402', 'name' => 'Business Law', 'units' => 3, 'is_general' => false],
        ];

        foreach ($bsbaCourses as $courseData) {
            $course = Course::create($courseData);
            $course->programs()->attach($bsba->id);
        }

        // Create Current Semester
        $currentSemester = Semester::create([
            'name' => 'First Semester 2025-26',
            'code' => '1-2025-26',
            'start_date' => '2025-08-01',
            'end_date' => '2025-12-15',
            'is_current' => true,
        ]);

        // Create Admin User
        User::create([
            'name' => 'System Administrator',
            'email' => 'admin@university.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'student_id' => null,
        ]);

        // Create Professor Users 
        $professor1 = User::create([
            'name' => 'Prof. Glenn Oliva',
            'email' => 'g.oliva@university.com',
            'password' => Hash::make('password'),
            'role' => 'professor',
            'student_id' => null,
        ]);

        $professor2 = User::create([
            'name' => 'Prof. Lisa Minci',
            'email' => 'l.minci@university.com',
            'password' => Hash::make('password'),
            'role' => 'professor',
            'student_id' => null,
        ]);

        $professor3 = User::create([
            'name' => 'Prof. Albedo Kreideprinz',
            'email' => 'a.kreideprinz@university.com',
            'password' => Hash::make('password'),
            'role' => 'professor',
            'student_id' => null,
        ]);

        $professor4 = User::create([
            'name' => 'Prof. Ningguang',
            'email' => 'ningguang@university.com',
            'password' => Hash::make('password'),
            'role' => 'professor',
            'student_id' => null,
        ]);

        // Create Student Users
        $student1 = User::create([
            'name' => 'Kamisato Ayaka',
            'email' => 'ayaka@university.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'program_id' => $bsit->id,
            'student_id' => '2024-00001',
            'year_level' => '2nd Year',
        ]);

        $student2 = User::create([
            'name' => 'Raiden Shogun',
            'email' => 'raiden@university.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'program_id' => $bsit->id,
            'student_id' => '2024-00002',
            'year_level' => '2nd Year',
        ]);

        $student3 = User::create([
            'name' => 'Zhongli',
            'email' => 'zhongli@university.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'program_id' => $bscs->id,
            'student_id' => '2024-00003',
            'year_level' => '1st Year',
        ]);

        $student4 = User::create([
            'name' => 'Bennett',
            'email' => 'bennett@university.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'program_id' => $bsit->id,
            'student_id' => '2024-00004',
            'year_level' => '1st Year',
        ]);

        $student5 = User::create([
            'name' => 'Noelle',
            'email' => 'noelle@university.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'program_id' => $bsba->id,
            'student_id' => '2024-00005',
            'year_level' => '2nd Year',
        ]);

        $student6 = User::create([
            'name' => 'Sucrose',
            'email' => 'sucrose@university.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'program_id' => $bscs->id,
            'student_id' => '2024-00006',
            'year_level' => '3rd Year',
        ]);

        $student7 = User::create([
            'name' => 'Fischl',
            'email' => 'fischl@university.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'program_id' => $bsit->id,
            'student_id' => '2024-00007',
            'year_level' => '2nd Year',
        ]);

        $student8 = User::create([
            'name' => 'Klee',
            'email' => 'klee@university.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'program_id' => $bsit->id,
            'student_id' => '2024-00008',
            'year_level' => '1st Year',
        ]);

        $student9 = User::create([
            'name' => 'Xingqiu',
            'email' => 'xingqiu@university.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'program_id' => $bsba->id,
            'student_id' => '2024-00009',
            'year_level' => '2nd Year',
        ]);

        $student10 = User::create([
            'name' => 'Barbara',
            'email' => 'barbara@university.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'program_id' => $bsba->id,
            'student_id' => '2024-00010',
            'year_level' => '1st Year',
        ]);

        $student11 = User::create([
            'name' => 'Diona',
            'email' => 'diona@university.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'program_id' => $bsit->id,
            'student_id' => '2024-00011',
            'year_level' => '1st Year',
        ]);

        $student12 = User::create([
            'name' => 'Qiqi',
            'email' => 'qiqi@university.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'program_id' => $bscs->id,
            'student_id' => '2024-00012',
            'year_level' => '3rd Year',
        ]);

        $student13 = User::create([
            'name' => 'John Lloyd Majarucon',
            'email' => 'johnlloyd.majarucon@university.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'program_id' => $bsit->id,
            'student_id' => '2024-00013',
            'year_level' => '3rd Year',
        ]);

        $student14 = User::create([
            'name' => 'Alfred Jeff Catacutan',
            'email' => 'alfredjeff.catacutan@university.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'program_id' => $bsit->id,
            'student_id' => '2024-00014',
            'year_level' => '2nd Year',
        ]);

        $student15 = User::create([
            'name' => 'Lanabelle Juanes',
            'email' => 'lanabelle.juanes@university.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'program_id' => $bsba->id,
            'student_id' => '2024-00015',
            'year_level' => '1st Year',
        ]);

        $student16 = User::create([
            'name' => 'Yanabelle Juanes',
            'email' => 'yanabelle.juanes@university.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'program_id' => $bsba->id,
            'student_id' => '2024-00016',
            'year_level' => '1st Year',
        ]);

        // Create Course Sections for Current Semester
        $it101 = Course::where('course_code', 'IT101')->first();
        $it102 = Course::where('course_code', 'IT102')->first();
        $it103 = Course::where('course_code', 'IT103')->first();
        $it201 = Course::where('course_code', 'IT201')->first();
        $it202 = Course::where('course_code', 'IT202')->first();
        $it203 = Course::where('course_code', 'IT203')->first();
        $it204 = Course::where('course_code', 'IT204')->first();
        $it205 = Course::where('course_code', 'IT205')->first();
        $it301 = Course::where('course_code', 'IT301')->first();
        $it302 = Course::where('course_code', 'IT302')->first();
        $it303 = Course::where('course_code', 'IT303')->first();
        $it304 = Course::where('course_code', 'IT304')->first();
        $ge101 = Course::where('course_code', 'GE101')->first();
        $ge102 = Course::where('course_code', 'GE102')->first();
        $ge103 = Course::where('course_code', 'GE103')->first();
        $pe101 = Course::where('course_code', 'PE101')->first();
        $ba101 = Course::where('course_code', 'BA101')->first();
        $ba102 = Course::where('course_code', 'BA102')->first();
        $ba201 = Course::where('course_code', 'BA201')->first();
        $ba202 = Course::where('course_code', 'BA202')->first();
        $ba302 = Course::where('course_code', 'BA302')->first();

        // IT101 Sections - MULTIPLE SECTIONS
        CourseSection::create([
            'course_id' => $it101->id,
            'professor_id' => $professor1->id,
            'semester_id' => $currentSemester->id,
            'section_code' => '0001',
            'max_students' => 40,
            'schedule' => '730M-930M M-F',
            'room' => 'Room 301',
        ]);

        CourseSection::create([
            'course_id' => $it101->id,
            'professor_id' => $professor1->id,
            'semester_id' => $currentSemester->id,
            'section_code' => '0002',
            'max_students' => 40,
            'schedule' => '100A-230A M-F',
            'room' => 'Room 302',
        ]);

        CourseSection::create([
            'course_id' => $it101->id,
            'professor_id' => $professor2->id,
            'semester_id' => $currentSemester->id,
            'section_code' => '0003',
            'max_students' => 35,
            'schedule' => '500E-700E M-F',
            'room' => 'Room 303',
        ]);

        // IT102 Sections - MULTIPLE SECTIONS
        CourseSection::create([
            'course_id' => $it102->id,
            'professor_id' => $professor2->id,
            'semester_id' => $currentSemester->id,
            'section_code' => '0004',
            'max_students' => 35,
            'schedule' => '1000M-1100M M-F',
            'room' => 'Lab 201',
        ]);

        CourseSection::create([
            'course_id' => $it102->id,
            'professor_id' => $professor2->id,
            'semester_id' => $currentSemester->id,
            'section_code' => '0005',
            'max_students' => 35,
            'schedule' => '200A-300A M-F',
            'room' => 'Lab 202',
        ]);

        CourseSection::create([
            'course_id' => $it102->id,
            'professor_id' => $professor3->id,
            'semester_id' => $currentSemester->id,
            'section_code' => '0006',
            'max_students' => 30,
            'schedule' => '600E-800E M-F',
            'room' => 'Lab 203',
        ]);

        // IT103 Sections
        CourseSection::create([
            'course_id' => $it103->id,
            'professor_id' => $professor3->id,
            'semester_id' => $currentSemester->id,
            'section_code' => '0007',
            'max_students' => 35,
            'schedule' => '900M-1030M M-F',
            'room' => 'Lab 204',
        ]);

        CourseSection::create([
            'course_id' => $it103->id,
            'professor_id' => $professor3->id,
            'semester_id' => $currentSemester->id,
            'section_code' => '0008',
            'max_students' => 35,
            'schedule' => '230A-400A M-F',
            'room' => 'Lab 205',
        ]);

        // IT201 Sections - MULTIPLE SECTIONS
        CourseSection::create([
            'course_id' => $it201->id,
            'professor_id' => $professor1->id,
            'semester_id' => $currentSemester->id,
            'section_code' => '0009',
            'max_students' => 35,
            'schedule' => '900M-1030M M-F',
            'room' => 'Lab 202',
        ]);

        CourseSection::create([
            'course_id' => $it201->id,
            'professor_id' => $professor1->id,
            'semester_id' => $currentSemester->id,
            'section_code' => '0010',
            'max_students' => 35,
            'schedule' => '300A-400A M-F',
            'room' => 'Lab 203',
        ]);

        // IT202 Sections (Web Development) - MULTIPLE SECTIONS
        CourseSection::create([
            'course_id' => $it202->id,
            'professor_id' => $professor3->id,
            'semester_id' => $currentSemester->id,
            'section_code' => '0011',
            'max_students' => 30,
            'schedule' => '100A-200A M-F',
            'room' => 'Lab 203',
        ]);

        CourseSection::create([
            'course_id' => $it202->id,
            'professor_id' => $professor3->id,
            'semester_id' => $currentSemester->id,
            'section_code' => '0012',
            'max_students' => 30,
            'schedule' => '500E-700E M-F',
            'room' => 'Lab 204',
        ]);

        CourseSection::create([
            'course_id' => $it202->id,
            'professor_id' => $professor2->id,
            'semester_id' => $currentSemester->id,
            'section_code' => '0013',
            'max_students' => 30,
            'schedule' => '800M-930M M-F',
            'room' => 'Lab 205',
        ]);

        // IT203 Sections (Database Management) - MULTIPLE SECTIONS
        CourseSection::create([
            'course_id' => $it203->id,
            'professor_id' => $professor3->id,
            'semester_id' => $currentSemester->id,
            'section_code' => '0014',
            'max_students' => 30,
            'schedule' => '1030A-1200A M-F',
            'room' => 'Lab 204',
        ]);

        CourseSection::create([
            'course_id' => $it203->id,
            'professor_id' => $professor3->id,
            'semester_id' => $currentSemester->id,
            'section_code' => '0015',
            'max_students' => 30,
            'schedule' => '230A-400A M-F',
            'room' => 'Lab 206',
        ]);

        // IT204 Sections (Network Fundamentals) - MULTIPLE SECTIONS
        CourseSection::create([
            'course_id' => $it204->id,
            'professor_id' => $professor1->id,
            'semester_id' => $currentSemester->id,
            'section_code' => '0016',
            'max_students' => 35,
            'schedule' => '200A-300A M-F',
            'room' => 'Room 303',
        ]);

        CourseSection::create([
            'course_id' => $it204->id,
            'professor_id' => $professor1->id,
            'semester_id' => $currentSemester->id,
            'section_code' => '0017',
            'max_students' => 35,
            'schedule' => '600E-800E M-F',
            'room' => 'Room 304',
        ]);

        // IT205 Sections (Object-Oriented Programming)
        CourseSection::create([
            'course_id' => $it205->id,
            'professor_id' => $professor2->id,
            'semester_id' => $currentSemester->id,
            'section_code' => '0018',
            'max_students' => 35,
            'schedule' => '800M-930M M-F',
            'room' => 'Lab 207',
        ]);

        CourseSection::create([
            'course_id' => $it205->id,
            'professor_id' => $professor2->id,
            'semester_id' => $currentSemester->id,
            'section_code' => '0019',
            'max_students' => 35,
            'schedule' => '100A-230A M-F',
            'room' => 'Lab 208',
        ]);

        // IT303 Sections (Mobile Application Development) - MULTIPLE SECTIONS
        CourseSection::create([
            'course_id' => $it303->id,
            'professor_id' => $professor3->id,
            'semester_id' => $currentSemester->id,
            'section_code' => '0020',
            'max_students' => 30,
            'schedule' => '500E-700E M-F',
            'room' => 'Lab 205',
        ]);

        CourseSection::create([
            'course_id' => $it303->id,
            'professor_id' => $professor3->id,
            'semester_id' => $currentSemester->id,
            'section_code' => '0021',
            'max_students' => 30,
            'schedule' => '300A-400A M-F',
            'room' => 'Lab 206',
        ]);

        CourseSection::create([
            'course_id' => $it303->id,
            'professor_id' => $professor2->id,
            'semester_id' => $currentSemester->id,
            'section_code' => '0022',
            'max_students' => 30,
            'schedule' => '1000M-1100M M-F',
            'room' => 'Lab 209',
        ]);

        // BA101 Sections (Fundamentals of Accounting) - MULTIPLE SECTIONS
        CourseSection::create([
            'course_id' => $ba101->id,
            'professor_id' => $professor4->id,
            'semester_id' => $currentSemester->id,
            'section_code' => '0023',
            'max_students' => 45,
            'schedule' => '800M-900M M-F',
            'room' => 'Room 201',
        ]);

        CourseSection::create([
            'course_id' => $ba101->id,
            'professor_id' => $professor4->id,
            'semester_id' => $currentSemester->id,
            'section_code' => '0024',
            'max_students' => 45,
            'schedule' => '100A-200A M-F',
            'room' => 'Room 202',
        ]);

        CourseSection::create([
            'course_id' => $ba101->id,
            'professor_id' => $professor4->id,
            'semester_id' => $currentSemester->id,
            'section_code' => '0025',
            'max_students' => 45,
            'schedule' => '500E-700E M-F',
            'room' => 'Room 203',
        ]);

        // BA102 Sections (Principles of Management) - MULTIPLE SECTIONS
        CourseSection::create([
            'course_id' => $ba102->id,
            'professor_id' => $professor4->id,
            'semester_id' => $currentSemester->id,
            'section_code' => '0026',
            'max_students' => 45,
            'schedule' => '230A-400A M-F',
            'room' => 'Room 202',
        ]);

        CourseSection::create([
            'course_id' => $ba102->id,
            'professor_id' => $professor4->id,
            'semester_id' => $currentSemester->id,
            'section_code' => '0027',
            'max_students' => 45,
            'schedule' => '800M-930M M-F',
            'room' => 'Room 204',
        ]);

        // BA201 Sections (Marketing Management) - MULTIPLE SECTIONS
        CourseSection::create([
            'course_id' => $ba201->id,
            'professor_id' => $professor4->id,
            'semester_id' => $currentSemester->id,
            'section_code' => '0028',
            'max_students' => 40,
            'schedule' => '300A-400A M-F',
            'room' => 'Room 203',
        ]);

        CourseSection::create([
            'course_id' => $ba201->id,
            'professor_id' => $professor4->id,
            'semester_id' => $currentSemester->id,
            'section_code' => '0029',
            'max_students' => 40,
            'schedule' => '600E-800E M-F',
            'room' => 'Room 205',
        ]);

        // BA202 Sections (Financial Management)
        CourseSection::create([
            'course_id' => $ba202->id,
            'professor_id' => $professor4->id,
            'semester_id' => $currentSemester->id,
            'section_code' => '0030',
            'max_students' => 40,
            'schedule' => '900M-1030M M-F',
            'room' => 'Room 206',
        ]);

        CourseSection::create([
            'course_id' => $ba202->id,
            'professor_id' => $professor4->id,
            'semester_id' => $currentSemester->id,
            'section_code' => '0031',
            'max_students' => 40,
            'schedule' => '200A-300A M-F',
            'room' => 'Room 207',
        ]);

        // BA302 Sections (Entrepreneurship) - MULTIPLE SECTIONS
        CourseSection::create([
            'course_id' => $ba302->id,
            'professor_id' => $professor4->id,
            'semester_id' => $currentSemester->id,
            'section_code' => '0032',
            'max_students' => 40,
            'schedule' => '600E-800E M-F',
            'room' => 'Room 204',
        ]);

        CourseSection::create([
            'course_id' => $ba302->id,
            'professor_id' => $professor4->id,
            'semester_id' => $currentSemester->id,
            'section_code' => '0033',
            'max_students' => 40,
            'schedule' => '100A-230A M-F',
            'room' => 'Room 208',
        ]);

        CourseSection::create([
            'course_id' => $ba302->id,
            'professor_id' => $professor4->id,
            'semester_id' => $currentSemester->id,
            'section_code' => '0034',
            'max_students' => 40,
            'schedule' => '800M-930M M-F',
            'room' => 'Room 209',
        ]);

        // General Education Sections - MULTIPLE SECTIONS
        CourseSection::create([
            'course_id' => $ge101->id,
            'professor_id' => null,
            'semester_id' => $currentSemester->id,
            'section_code' => '0035',
            'max_students' => 50,
            'schedule' => '800M-900M M-F',
            'room' => 'Room 101',
        ]);

        CourseSection::create([
            'course_id' => $ge101->id,
            'professor_id' => null,
            'semester_id' => $currentSemester->id,
            'section_code' => '0036',
            'max_students' => 50,
            'schedule' => '100A-200A M-F',
            'room' => 'Room 102',
        ]);

        CourseSection::create([
            'course_id' => $ge101->id,
            'professor_id' => null,
            'semester_id' => $currentSemester->id,
            'section_code' => '0037',
            'max_students' => 50,
            'schedule' => '500E-700E M-F',
            'room' => 'Room 103',
        ]);

        CourseSection::create([
            'course_id' => $ge102->id,
            'professor_id' => null,
            'semester_id' => $currentSemester->id,
            'section_code' => '0038',
            'max_students' => 50,
            'schedule' => '800M-930M M-F',
            'room' => 'Room 102',
        ]);

        CourseSection::create([
            'course_id' => $ge102->id,
            'professor_id' => null,
            'semester_id' => $currentSemester->id,
            'section_code' => '0039',
            'max_students' => 50,
            'schedule' => '230A-400A M-F',
            'room' => 'Room 104',
        ]);

        CourseSection::create([
            'course_id' => $ge103->id,
            'professor_id' => null,
            'semester_id' => $currentSemester->id,
            'section_code' => '0040',
            'max_students' => 50,
            'schedule' => '900M-1030M M-F',
            'room' => 'Room 105',
        ]);

        CourseSection::create([
            'course_id' => $ge103->id,
            'professor_id' => null,
            'semester_id' => $currentSemester->id,
            'section_code' => '0041',
            'max_students' => 50,
            'schedule' => '300A-400A M-F',
            'room' => 'Room 106',
        ]);

        CourseSection::create([
            'course_id' => $pe101->id,
            'professor_id' => null,
            'semester_id' => $currentSemester->id,
            'section_code' => '0042',
            'max_students' => 60,
            'schedule' => '300A-400A M-F',
            'room' => 'Gymnasium',
        ]);

        CourseSection::create([
            'course_id' => $pe101->id,
            'professor_id' => null,
            'semester_id' => $currentSemester->id,
            'section_code' => '0043',
            'max_students' => 60,
            'schedule' => '600E-800E M-F',
            'room' => 'Gymnasium',
        ]);

        // Create Announcements
        Announcement::create([
            'title' => 'Welcome to First Semester 2025-26',
            'content' => 'Welcome to the new semester! Please ensure you complete your enrollment and payment before the deadline.',
            'target_audience' => 'all',
            'is_active' => true,
        ]);

        Announcement::create([
            'title' => 'Enrollment Period Extended',
            'content' => 'The enrollment period has been extended until December 15, 2025. Please complete your enrollment as soon as possible.',
            'target_audience' => 'students',
            'is_active' => true,
        ]);

        Announcement::create([
            'title' => 'Grade Submission Deadline',
            'content' => 'All professors must submit final grades by December 20, 2025.',
            'target_audience' => 'professors',
            'is_active' => true,
        ]);

        Announcement::create([
            'title' => 'New Course Offerings Available',
            'content' => 'Check out our new BSIT and BSBA courses for this semester including Mobile Application Development and International Business.',
            'target_audience' => 'students',
            'is_active' => true,
        ]);

        Announcement::create([
            'title' => 'Faculty Meeting Schedule',
            'content' => 'Monthly faculty meeting will be held on November 15, 2025 at 3:00 PM in the Conference Room.',
            'target_audience' => 'professors',
            'is_active' => true,
        ]);

        // Seed Assessment Fees
        $this->call(AssessmentFeeSeeder::class);
    }
}
