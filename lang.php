<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'ar';
}

if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'ar'])) {
    $_SESSION['lang'] = $_GET['lang'];
    $url = strtok($_SERVER["REQUEST_URI"], '?');
    header("Location: " . $url);
    exit();
}

$lang = $_SESSION['lang'];

$translations = [
    'en' => [
        'dir' => 'ltr',
        'font' => "'Poppins', sans-serif",
        'title' => 'MEW ISC | Meeting Schedules',
        'mew_kuwait' => 'MEW ISC',
        'home' => 'Home',
        'eservices' => 'ISC Portals',
        'announcements' => 'IT Memos',
        'contact' => 'Helpdesk',
        'login' => 'Staff Login',
        'logout' => 'Log Out',
        'lang_btn' => 'العربية',
        'lang_toggle' => 'ar',
        'emergency' => 'ISC IT Helpdesk: Internal Ext. 152',
        'hero_title' => 'Information Systems Center',
        'hero_desc' => 'Manage and track your official IT meetings, projects, and center-wide schedules efficiently.',
        'view_schedule' => 'My Schedule',
        'explore_services' => 'ISC Resources',
        'quick_services' => 'ISC Quick Links',
        'meeting_rooms' => 'Meeting Rooms',
        'meeting_rooms_desc' => 'Book and check availability of Information Systems Center conference rooms.',
        'it_support' => 'IT Support',
        'it_support_desc' => 'Request technical support for meetings, presentations, and devices.',
        'calendar' => 'Corporate Calendar',
        'calendar_desc' => 'View the center-wide calendar for upcoming software deployments and maintenance.',
        'reports' => 'System Reports',
        'reports_desc' => 'Access weekly server status, network uptime, and project deliverables.',
        'footer' => '© 2026 MEW Information Systems Center - State of Kuwait. All rights reserved.',
        'login_title' => 'ISC Staff Login',
        'username' => 'Username',
        'password' => 'Password',
        'back_home' => 'Back to Home',
        'invalid_pass' => 'Invalid Password.',
        'username_not_found' => 'Username not found in staff directory.',
        'all_schedules' => 'Meeting Schedules',
        'access_denied' => 'Access Denied',
        'access_denied_desc' => 'You do not have the required permissions (Admin/Moderator) to view this page.',
        'employee_name' => 'Employee Name',
        'user_role' => 'Role:',
        'welcome' => 'Welcome,',
        'your_meetings' => 'Your Meeting Schedule',
        'meeting_title' => 'Meeting Title',
        'date' => 'Date',
        'time' => 'Time',
        'room' => 'Room',
        'status' => 'Status',
        'action' => 'Action',
        'join' => 'Details',
        'upcoming' => 'Upcoming',
        'completed' => 'Completed',
        'no_meetings' => 'You have no meetings scheduled.',
        'add_meeting' => 'Add New Meeting',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'save' => 'Save Meeting',
        'cancel' => 'Cancel',
        'confirm_delete' => 'Are you sure you want to delete this meeting?',
        'select_user' => '-- Select Employee --',
        'cancelled' => 'Cancelled'
    ],
    'ar' => [
        'dir' => 'rtl',
        'font' => "'Cairo', sans-serif",
        'title' => 'مركز نظم المعلومات | جداول الاجتماعات',
        'mew_kuwait' => 'نظم المعلومات',
        'home' => 'الرئيسية',
        'eservices' => 'بوابات المركز',
        'announcements' => 'التعاميم',
        'contact' => 'الدعم الفني',
        'login' => 'دخول الموظفين',
        'logout' => 'تسجيل الخروج',
        'lang_btn' => 'English',
        'lang_toggle' => 'en',
        'emergency' => 'مكتب مساعدة نظم المعلومات: داخلي 152',
        'hero_title' => 'مركز نظم المعلومات',
        'hero_desc' => 'إدارة ومتابعة اجتماعات قسم تكنولوجيا المعلومات، والمشاريع، والجداول الخاصة بالمركز بكفاءة.',
        'view_schedule' => 'جدول اجتماعاتي',
        'explore_services' => 'موارد المركز',
        'quick_services' => 'روابط سريعة للمركز',
        'meeting_rooms' => 'غرف الاجتماعات',
        'meeting_rooms_desc' => 'حجز والتحقق من توفر غرف الاجتماعات في مركز نظم المعلومات.',
        'it_support' => 'الدعم الفني',
        'it_support_desc' => 'طلب الدعم الفني للاجتماعات، العروض التقديمية، والأجهزة.',
        'calendar' => 'التقويم المؤسسي',
        'calendar_desc' => 'عرض التقويم الشامل للمركز للإصدارات البرمجية والصيانة القادمة.',
        'reports' => 'تقارير الأنظمة',
        'reports_desc' => 'الوصول إلى حالة الخوادم الأسبوعية، استقرار الشبكة، وتسليمات المشاريع.',
        'footer' => '© 2026 مركز نظم المعلومات - وزارة الكهرباء والماء. جميع الحقوق محفوظة.',
        'login_title' => 'دخول موظفي المركز',
        'username' => 'اسم المستخدم',
        'password' => 'كلمة المرور',
        'back_home' => 'العودة للرئيسية',
        'invalid_pass' => 'كلمة المرور غير صحيحة.',
        'username_not_found' => 'اسم المستخدم غير مسجل في دليل الموظفين.',
        'all_schedules' => 'جداول الاجتماعات',
        'access_denied' => 'تم رفض الوصول',
        'access_denied_desc' => 'لا تملك الصلاحيات المطلوبة (مسؤول/مشرف) لعرض هذه الصفحة.',
        'employee_name' => 'اسم الموظف',
        'user_role' => 'الصلاحية:',
        'welcome' => 'مرحباً بك،',
        'your_meetings' => 'جدول اجتماعاتك',
        'meeting_title' => 'عنوان الاجتماع',
        'date' => 'التاريخ',
        'time' => 'الوقت',
        'room' => 'القاعة / الغرفة',
        'status' => 'الحالة',
        'action' => 'الإجراء',
        'join' => 'التفاصيل',
        'upcoming' => 'قادم',
        'completed' => 'مكتمل',
        'no_meetings' => 'ليس لديك اجتماعات مجدولة.',
        'add_meeting' => 'إضافة اجتماع جديد',
        'edit' => 'تعديل',
        'delete' => 'حذف',
        'save' => 'حفظ الاجتماع',
        'cancel' => 'إلغاء',
        'confirm_delete' => 'هل أنت متأكد من حذف هذا الاجتماع؟',
        'select_user' => '-- اختر الموظف --',
        'cancelled' => 'ملغي'
    ]
];

function t($key) {
    global $translations, $lang;
    return $translations[$lang][$key] ?? $key;
}
?>