<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\PageSectionTranslation;

class PageSectionsSeeder extends Seeder
{
    public function run()
    {
        // Create frontend landing pages if they don't exist
        $pages = [
            ['name' => 'Home', 'slug' => 'home'],
            ['name' => 'About Us', 'slug' => 'about-us'],
            ['name' => 'News', 'slug' => 'news'],
        ];

        foreach ($pages as $pageData) {
            if (!Page::where('slug', $pageData['slug'])->exists()) {
                Page::create($pageData);
                $this->command->info("Created page: {$pageData['name']}");
            }
        }

        // Get the home page
        $homePage = Page::where('slug', 'home')->first();
        
        if (!$homePage) {
            $this->command->error('Unable to create home page!');
            return;
        }

        // Clear existing sections for home page
        PageSection::where('page_id', $homePage->id)->delete();

        // Section 1: Hero Section
        $heroSection = PageSection::create([
            'page_id' => $homePage->id,
            'section_type' => 'hero',
            'section_key' => 'home_hero',
            'order' => 1,
            'status' => 1,
        ]);

        PageSectionTranslation::create([
            'page_section_id' => $heroSection->id,
            'lang' => 'en',
            'title' => 'Justice Simplified. Anytime. Anywhere',
            'subtitle' => null,
            'description' => null,
            'button_text' => 'Explore Service',
            'button_link' => '/services',
        ]);

        // Add Arabic translation
        PageSectionTranslation::create([
            'page_section_id' => $heroSection->id,
            'lang' => 'ar',
            'title' => 'العدالة مبسطة. في أي وقت. في أي مكان',
            'subtitle' => null,
            'description' => null,
            'button_text' => 'استكشاف الخدمة',
            'button_link' => '/services',
        ]);

        // Section 2: Banner Image
        $bannerSection = PageSection::create([
            'page_id' => $homePage->id,
            'section_type' => 'banner',
            'section_key' => 'home_banner',
            'image' => 'assets/images/banner.png',
            'order' => 2,
            'status' => 1,
        ]);

        PageSectionTranslation::create([
            'page_section_id' => $bannerSection->id,
            'lang' => 'en',
            'title' => 'Banner',
        ]);

        // Section 3: Services/Choose Service Section
        $servicesSection = PageSection::create([
            'page_id' => $homePage->id,
            'section_type' => 'services',
            'section_key' => 'home_services',
            'order' => 3,
            'status' => 1,
        ]);

        PageSectionTranslation::create([
            'page_section_id' => $servicesSection->id,
            'lang' => 'en',
            'title' => 'Choose Service',
            'subtitle' => null,
            'description' => null,
        ]);

        PageSectionTranslation::create([
            'page_section_id' => $servicesSection->id,
            'lang' => 'ar',
            'title' => 'اختر الخدمة',
            'subtitle' => null,
            'description' => null,
        ]);

        // Section 4: Quote Section
        $quoteSection = PageSection::create([
            'page_id' => $homePage->id,
            'section_type' => 'custom',
            'section_key' => 'home_quote',
            'order' => 4,
            'status' => 1,
        ]);

        PageSectionTranslation::create([
            'page_section_id' => $quoteSection->id,
            'lang' => 'en',
            'title' => "The UAE's first 24/7 digital legal ecosystem, consultations, court requests, translation, and more… all in one tap",
        ]);

        PageSectionTranslation::create([
            'page_section_id' => $quoteSection->id,
            'lang' => 'ar',
            'title' => 'أول نظام قانوني رقمي على مدار الساعة طوال أيام الأسبوع في الإمارات، والاستشارات وطلبات المحاكم والترجمة والمزيد... كل ذلك بنقرة واحدة',
        ]);

        // Section 5: New Era of Legal Services
        $newEraSection = PageSection::create([
            'page_id' => $homePage->id,
            'section_type' => 'custom',
            'section_key' => 'home_new_era',
            'order' => 5,
            'status' => 1,
        ]);

        PageSectionTranslation::create([
            'page_section_id' => $newEraSection->id,
            'lang' => 'en',
            'title' => 'A New Era of Legal Services Has Arrived',
            'description' => "Justyta transforms the entire legal journey into a seamless digital experience.
From court & prosecution request submissions to legal consultations and translation services, every process is rebuilt around speed, accuracy, and accessibility.
Whether through our mobile app or web platform, clients and law firms access a 24/7 legal world designed to eliminate waiting lines, paperwork pressure, and unnecessary costs.
Justyta isn't another legal tool ….. it's the future legal hub of the UAE.",
        ]);

        PageSectionTranslation::create([
            'page_section_id' => $newEraSection->id,
            'lang' => 'ar',
            'title' => 'لقد وصل عصر جديد من الخدمات القانونية',
            'description' => 'تحول Justyta الرحلة القانونية بأكملها إلى تجربة رقمية سلسة.
من تقديم طلبات المحكمة والنيابة إلى الاستشارات القانونية وخدمات الترجمة، تم إعادة بناء كل عملية حول السرعة والدقة وإمكانية الوصول.
سواء من خلال تطبيق الهاتف المحمول أو منصة الويب، يصل العملاء ومكاتب المحاماة إلى عالم قانوني على مدار الساعة طوال أيام الأسبوع مصمم للقضاء على خطوط الانتظار وضغط الأوراق والتكاليف غير الضرورية.
Justyta ليست مجرد أداة قانونية أخرى.... إنها مركز قانوني مستقبلي للإمارات العربية المتحدة.',
        ]);

        // Section 6: Impact/Statistics Section
        $impactSection = PageSection::create([
            'page_id' => $homePage->id,
            'section_type' => 'custom',
            'section_key' => 'home_impact',
            'image' => 'assets/images/law-img.png',
            'order' => 6,
            'status' => 1,
        ]);

        PageSectionTranslation::create([
            'page_section_id' => $impactSection->id,
            'lang' => 'en',
            'title' => 'Your Time Matters. Your Rights Matter More. Justyta Delivers Both.',
            'subtitle' => 'The Impact of Justyta',
            'description' => 'If Procedures moves slow. You don\'t have to.',
        ]);

        PageSectionTranslation::create([
            'page_section_id' => $impactSection->id,
            'lang' => 'ar',
            'title' => 'وقتك مهم. حقوقك أهم. Justyta يقدم كليهما.',
            'subtitle' => 'تأثير Justyta',
            'description' => 'إذا كانت الإجراءات بطيئة. لا يتعين عليك ذلك.',
        ]);

        // Section 7: Legal Services Reimagined
        $reimaginedSection = PageSection::create([
            'page_id' => $homePage->id,
            'section_type' => 'custom',
            'section_key' => 'home_reimagined',
            'image' => 'assets/images/about-Image.png',
            'order' => 7,
            'status' => 1,
        ]);

        PageSectionTranslation::create([
            'page_section_id' => $reimaginedSection->id,
            'lang' => 'en',
            'title' => 'Legal Services, Reimagined for Simplicity',
            'subtitle' => 'Law Firm Services',
            'description' => "Justyta makes complex legal procedures fast, transparent, and effortless. Our platform covers the full spectrum of legal needs.

Justyta, Designed for speed, Built for accuracy, Powered by trust. That's How Justice Simplified.",
            'button_text' => 'Explore Service',
            'button_link' => '/services',
        ]);

        PageSectionTranslation::create([
            'page_section_id' => $reimaginedSection->id,
            'lang' => 'ar',
            'title' => 'الخدمات القانونية، أعيد تصورها من أجل البساطة',
            'subtitle' => 'خدمات مكتب المحاماة',
            'description' => 'تجعل Justyta الإجراءات القانونية المعقدة سريعة وشفافة وسهلة. تغطي منصتنا الطيف الكامل من الاحتياجات القانونية.

Justyta، مصممة للسرعة، مبنية على الدقة، مدعومة بالثقة. هكذا تبسطت العدالة.',
            'button_text' => 'استكشاف الخدمة',
            'button_link' => '/services',
        ]);

        // Section 8: App Download
        $appDownloadSection = PageSection::create([
            'page_id' => $homePage->id,
            'section_type' => 'custom',
            'section_key' => 'home_app_download',
            'image' => 'assets/images/app-mockup.png',
            'order' => 8,
            'status' => 1,
        ]);

        PageSectionTranslation::create([
            'page_section_id' => $appDownloadSection->id,
            'lang' => 'en',
            'title' => 'Take the Power of Legal Access Everywhere, Anytime.',
            'description' => 'Download Justyta and access legal services whenever you need them no appointments, no lines, no delays.',
        ]);

        PageSectionTranslation::create([
            'page_section_id' => $appDownloadSection->id,
            'lang' => 'ar',
            'title' => 'خذ قوة الوصول القانوني في كل مكان، في أي وقت.',
            'description' => 'قم بتنزيل Justyta واحصل على الخدمات القانونية كلما احتجت إليها بدون مواعيد، بدون خطوط، بدون تأخير.',
        ]);

        $this->command->info('✓ Home page sections seeded successfully!');
        
        // ========================================
        // ABOUT US PAGE SECTIONS
        // ========================================
        
        $aboutPage = Page::where('slug', 'about-us')->first();
        
        if (!$aboutPage) {
            $this->command->error('About Us page not found!');
            return;
        }
        
        // Clear existing sections for about page
        PageSection::where('page_id', $aboutPage->id)->delete();
        
        // Section 1: Main About Section
        $aboutMainSection = PageSection::create([
            'page_id' => $aboutPage->id,
            'section_type' => 'custom',
            'section_key' => 'about_main',
            'image' => 'images/about-Image.png',
            'order' => 1,
            'status' => 1,
        ]);
        
        PageSectionTranslation::create([
            'page_section_id' => $aboutMainSection->id,
            'lang' => 'en',
            'title' => 'A New Standard for Legal Access in the UAE',
            'subtitle' => 'About Us',
            'description' => "Who We Are

We are a UAE-based legal-tech company committed to transforming how individuals and businesses interact with the legal system. Our platform works in partnership with licensed certified legal service providers, certified translators, and industry experts to ensure accuracy, compliance, and trust at every step.

From our app to our web platform, every feature is engineered to match the UAE's digital transformation vision, while supporting both clients and legal professionals with a smoother, smarter experience.

Justyta was built with one mission:
to make legal services faster, clearer, and accessible to everyone, anytime, anywhere with an affordable price.

In a world where time is valuable and legal procedures are often slow and complicated, Justyta reimagines the entire journey. We combine modern technology, verified legal professionals, automated processes, and user-first design to offer a digital platform that simplifies everything from court submissions to legal consultations.

We believe justice shouldn't be delayed by paperwork, crowded offices, or limited working hours.

Justyta removes the obstacles, leaving only clarity, speed, affordability.

What We Do
Justyta provides a full digital ecosystem of essential legal services:
• Law Firm Services
• Court & Public Prosecution Request Submissions
• Legal Consultations
• Legal Translation
• Training opportunities & Job Listings for Professional Development
• Annual Retainership Agreements for Businesses",
        ]);
        
        PageSectionTranslation::create([
            'page_section_id' => $aboutMainSection->id,
            'lang' => 'ar',
            'title' => 'معيار جديد للوصول القانوني في الإمارات',
            'subtitle' => 'من نحن',
            'description' => "من نحن

نحن شركة تكنولوجيا قانونية مقرها الإمارات ملتزمة بتحويل كيفية تفاعل الأفراد والشركات مع النظام القانوني. تعمل منصتنا بالشراكة مع مقدمي الخدمات القانونية المعتمدين والمترجمين المعتمدين وخبراء الصناعة لضمان الدقة والامتثال والثقة في كل خطوة.

من تطبيقنا إلى منصتنا على الويب، تم تصميم كل ميزة لتتناسب مع رؤية التحول الرقمي في الإمارات، مع دعم العملاء والمهنيين القانونيين بتجربة أكثر سلاسة وذكاءً.

تم بناء Justyta بمهمة واحدة:
جعل الخدمات القانونية أسرع وأوضح ويمكن الوصول إليها للجميع في أي وقت وفي أي مكان بسعر معقول.

في عالم يكون فيه الوقت ثميناً والإجراءات القانونية غالباً بطيئة ومعقدة، تعيد Justyta تصور الرحلة بأكملها. نحن نجمع بين التكنولوجيا الحديثة والمهنيين القانونيين المعتمدين والعمليات الآلية والتصميم الموجه للمستخدم لتقديم منصة رقمية تبسط كل شيء من تقديم المحكمة إلى الاستشارات القانونية.

ماذا نفعل
توفر Justyta نظاماً رقمياً كاملاً للخدمات القانونية الأساسية:
• خدمات مكتب المحاماة
• تقديم طلبات المحكمة والنيابة العامة
• الاستشارات القانونية
• الترجمة القانونية
• فرص التدريب وقوائم الوظائف للتطوير المهني
• اتفاقيات الاحتفاظ السنوية للشركات",
        ]);
        
        // Section 2: Our Vision
        $visionSection = PageSection::create([
            'page_id' => $aboutPage->id,
            'section_type' => 'features',
            'section_key' => 'about_vision',
            'order' => 2,
            'status' => 1,
        ]);
        
        PageSectionTranslation::create([
            'page_section_id' => $visionSection->id,
            'lang' => 'en',
            'title' => 'Our Vision',
            'description' => "To become the UAE's leading digital legal hub trusted by clients, preferred by law firms, and recognized for setting a new global benchmark in legal accessibility.",
        ]);
        
        PageSectionTranslation::create([
            'page_section_id' => $visionSection->id,
            'lang' => 'ar',
            'title' => 'رؤيتنا',
            'description' => "أن نصبح المركز القانوني الرقمي الرائد في الإمارات، موثوقاً من قبل العملاء، ومفضلاً من قبل مكاتب المحاماة، ومعترفاً به لوضع معيار عالمي جديد في إمكانية الوصول القانوني.",
        ]);
        
        // Section 3: Why We Exist
        $whyExistSection = PageSection::create([
            'page_id' => $aboutPage->id,
            'section_type' => 'features',
            'section_key' => 'about_why_exist',
            'order' => 3,
            'status' => 1,
        ]);
        
        PageSectionTranslation::create([
            'page_section_id' => $whyExistSection->id,
            'lang' => 'en',
            'title' => 'Why We Exist',
            'description' => "Legal services should empower—not overwhelm.
By placing advanced technology behind every step of the legal workflow, Justyta provides:

• Speed without compromise
• Accuracy without delay
• Transparency without complexity
• 24/7 access without limitations

Our purpose is simple:
Bring justice closer to everyone, and give legal professionals a platform that amplifies their reach and impact.

With Justyta …. Justice Simplified",
        ]);
        
        PageSectionTranslation::create([
            'page_section_id' => $whyExistSection->id,
            'lang' => 'ar',
            'title' => 'لماذا نحن موجودون',
            'description' => "يجب أن تمكّن الخدمات القانونية - وليس أن تطغى.
من خلال وضع التكنولوجيا المتقدمة وراء كل خطوة من سير العمل القانوني، توفر Justyta:

• السرعة دون تنازل
• الدقة دون تأخير
• الشفافية دون تعقيد
• الوصول على مدار الساعة طوال أيام الأسبوع دون قيود

هدفنا بسيط:
تقريب العدالة من الجميع، ومنح المهنيين القانونيين منصة تضخم نطاق وصولهم وتأثيرهم.

مع Justyta ... العدالة مبسطة",
        ]);
        
        $this->command->info('✓ About Us page sections seeded successfully!');
    }
}

