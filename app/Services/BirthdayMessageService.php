<?php

namespace App\Services;

use App\Models\Student;

class BirthdayMessageService
{
    private array $templates = [
        // 1 — Pantun + Doa + Lelucon
        "Ada pantun dulu ya:\n\"Makan bakso di warung pojok, sambalnya pedas bikin nagih~\nUmur bertambah, ilmu makin oke, semoga masa depanmu makin indah!\"\n\n😄 Btw, tambah umur itu kayak update HP — kadang bikin bingung, tapi lama-lama jadi lebih canggih!\n\n🤲 Semoga Allah SWT senantiasa memberikan kesehatan, kemudahan dalam belajar, dan dijauhkan dari segala kesulitan. Tetap semangat, makin berprestasi, dan jadi kebanggaan orang tua ya, :name! 💪",

        // 2 — Lelucon + Pantun + Penyemangat
        "HAPPY BIRTHDAY, :name! Selamat ya, udah berhasil survive hidup sampai hari ini! 🥳\n\nFun fact: Setiap kamu tiup lilin ultah, berarti kamu udah kasih CO₂ ke udara. Guru kimia pasti bangga! 😂\n\nPantun spesial:\n\"Pergi ke pasar beli tempe goreng, dimakan bareng sambil nonton drakor~\nSemangat belajar jangan sampai loyo, nanti sukses orang tua ikut bangga!\"\n\n🌟 Semoga harimu penuh kebahagiaan, doamu dikabulkan, dan tahun ini jadi tahun terbaik dalam hidupmu! Gasss! 🚀",

        // 3 — Gaul + Doa Panjang Terstruktur
        "Eh :name, ultah nih? No way! Rasanya baru kemarin masih bingung bedain mana tanda tanya mana tanda seru, sekarang udah makin kece! 😎\n\n🙏 Doa dari sekolah:\nSemoga di usiamu yang baru ini kamu diberikan:\n• Kesehatan yang selalu terjaga 💪\n• Otak yang makin encer buat belajar 🧠\n• Rezeki yang lancar buat jajan 😂\n• Hati yang tenang ngadepin ujian 🎯\n• Masa depan yang cerah kayak layar HP kamu! ✨\n\nTetap jadi versi terbaik kamu ya! Kamu luar biasa! 🌈",

        // 4 — Motivasi + Pantun Edison
        ":name, SELAMAT ULANG TAHUN! Level naik lagi nih! 🏆\n\nPantun penyemangat:\n\"Jalan-jalan ke kota Medan, oleh-olehnya bika ambon~\nWalau hidup penuh rintangan, senyum terus ntar juga kelar kok!\"\n\n💡 Ingat ya — Thomas Edison gagal 1.000 kali sebelum nemuin bola lampu. Kamu baru berapa kali? Santai, masih banyak kesempatan! 😄\n\n🤲 Semoga Allah selalu membimbing :name, memudahkan setiap langkah, dan menjadikanmu anak yang sholeh/sholehah serta berguna bagi nusa dan bangsa. Aamiin! 🌙",

        // 5 — Teman Sebaya Vibes + Doa Mendalam
        "Selamat Ulang Tahun :name!! Gila, udah segede ini aja! 😲\n\nLelucon hari ini:\n\"Kenapa buku pelajaran selalu tebal?\"\n\"Karena kalau tipis, kamu nggak punya bantal tidur di kelas!\" 😂😂\n\nOkay serius deh —\n\n🌺 Di hari spesialmu ini, semoga kamu selalu dalam lindungan Allah SWT, diberikan kesehatan jasmani dan rohani, dimudahkan dalam setiap ujian dan cobaan, serta dikelilingi orang-orang yang tulus menyayangimu.\n\n💫 Shine bright, :name! The world needs your light! ✨",

        // 6 — Pantun Dobel + Motivasi
        ":name, HAPPY BIRTHDAY! Selamat udah berhasil mengorbit matahari satu kali lagi! 🌍☀️\n\nPantun #1:\n\"Pagi hari minum teh hangat, ditemani roti selai coklat~\nSemoga hidupmu selalu semangat, raih cita-cita setinggi langit!\"\n\nPantun #2:\n\"Pergi berlayar ke tengah laut, ikan hiu lewat nggak bikin takut~\nHidup memang kadang bikin galut, asal jangan menyerah pasti ada jut!\"\n\n🙏 Doa tulus: Semoga Allah SWT memberikan kemudahan di setiap langkah, melapangkan rezeki, dan menjadikan :name pribadi yang berakhlak mulia. Tetap semangat menuntut ilmu ya! 📚",

        // 7 — Gen Z + Reminder List + Doa
        ":name, HAPPY BIRTHDAY BESTIE! 🔥\n\nPlot twist hari ini: Kamu makin tua, tapi tetap lebih muda dari guru-gurumu. Masih menang! 😝\n\nReminder:\n✅ Mimpimu valid\n✅ Kamu mampu\n✅ Ujian itu bisa dilewati\n✅ Jajan gratis di ultah itu WAJIB 😂\n\n🙏 Semoga Allah SWT senantiasa meridhoi setiap langkahmu, memberikan kesehatan, kebahagiaan, dan menjadikanmu anak yang membanggakan keluarga. Selamat berulang tahun ya! 🎊",

        // 8 — Filosofis + Pantun Nasihat
        "Selamat Ulang Tahun, :name!\n\nTau nggak? Setiap tahun kamu ultah, bumi udah keliling matahari sekali. Artinya kamu udah ke matahari berkali-kali — lebih sering dari astronot manapun! 🚀😂\n\nPantun Nasihat:\n\"Bunga melati harum semerbak, ditanam indah di taman surga~\nIlmu dan adab jangan sampai terombak, itulah bekal menuju mulia!\"\n\nTahun ini semoga :name:\n✅ Makin semangat belajar\n✅ Makin dekat dengan Allah\n✅ Makin baik dari sebelumnya\n✅ Dan makin keren tentunya! 😎\n\nHappy birthday, superstar! 🏆",

        // 9 — Santai + Mengharukan
        ":name, happy birthday ya!\n\nAku tahu kamu mungkin lagi ribet sama PR, ulangan, atau drama kehidupan remaja yang nggak ada habisnya 😅 — tapi izinkan hari ini jadi hari yang spesial buat kamu.\n\n💬 Kata-kata ini buat kamu:\n\"Kamu nggak perlu sempurna. Kamu cukup jadi kamu — yang terus berusaha, yang nggak menyerah, yang selalu bangkit setiap jatuh.\"\n\nPantun:\n\"Langit mendung matahari bersembunyi, tapi setelah hujan pasti ada pelangi~\nMeski hari ini terasa berat di hati, kamu kuat, badai pasti berlalu nanti!\"\n\n🤲 Semoga Allah selalu menemanimu di setiap langkah. Tetap strong! 💪",

        // 10 — Alpha Slang + Doa
        ":name! ULTAH? FR FR?! No cap, kamu udah makin glow up! 🔥\n\nSlay dulu, nanti nangis ujian belakangan 💅\n\nOkay serius:\nPantun buat kamu:\n\"Jajan bakso sambil ngobrol, sambalnya pedes bikin nagih terus~\nHidup mungkin sering bikin galau, tapi semangat kamu jangan sampai habis!\"\n\n💪 Kamu lahir untuk hal-hal yang besar. Satu langkah kecil hari ini = satu langkah menuju masa depan yang kamu impikan. Terus gasss ya!\n\n🙏 Semoga sehat, bahagia, dan selalu dalam lindungan Allah. Happy birthday bestie! 🎉",

        // 11 — Lelucon Lilin + Pantun + Doa Formal
        ":name, SELAMAT HARI JADI KAMU! 🎈\n\nLelucon:\n\"Apa bedanya kue ulang tahun sama buku pelajaran?\"\n\"Kue dimakan habis, tapi buku tetap sisa!\" 😂\n\nPantun:\n\"Ke pasar beli kangkung seikat, masak sayur bareng ikan asin~\nJaga semangat jangan sampai retak, masa depanmu sudah dijamin manis!\"\n\n🤲 Doa kami: Semoga Allah SWT memberikan keberkahan di usia barumu, melancarkan segala urusanmu, dan menjadikanmu pribadi yang berakhlak mulia. Aamiin ya Rabbal 'Aalamin. 🌙",

        // 12 — Pantun + Fakta Ultah + Doa Singkat
        "HAPPY BIRTHDAY :name! Selamat ya! 🎊\n\nFakta menarik: Di hari ultahmu, statistik menunjukkan kamu lebih tua dari 99% anak yang lahir setelahmu. Top 1% nih! 😄\n\nPantun Dobel:\n\"Pergi ke kebun metik timun, timunnya segar untuk lalapan~\nJangan biarkan impian terkubur, bangkit dan wujudkan harapan!\"\n\n\"Masak ayam pakai bumbu kuning, dihidangkan panas-panas bersama nasi~\nSemoga hatimu selalu bening, dan kebahagiaanmu abadi!\"\n\n🌙 Semoga Allah SWT senantiasa menjaga, melindungi, dan memberikan yang terbaik untuk hidupmu. Selamat ulang tahun! ❤️",

        // 13 — Panjang + Filosofi Sepeda + Pantun
        "Selamat Ulang Tahun, :name! Kamu tahu nggak?\n\nDi hari ini, bertahun-tahun yang lalu, seseorang yang sangat hebat lahir ke dunia. Dan orang itu adalah... ya, kamu! (Jangan GR dulu ya 😂)\n\nKata-kata penyemangat:\n\"Hidup itu kayak naik sepeda — biar nggak jatuh, kamu harus terus mengayuh!\"\n\nPantun:\n\"Beli kue di toko sebelah, rasanya manis bikin ketagihan~\nJangan takut salah dan jangan lemah, orang sukses lahir dari kegagalan!\"\n\n🌟 Selamat menikmati hari spesialmu! Semoga sehat, bahagia, dan segala kebaikan selalu menyertai :name! ❤️",

        // 14 — Ulang Tahun Serius + Pantun Panjang
        "Wahai :name yang berulang tahun!\n\nIzinkan kami mempersembahkan pantun spesial:\n\n\"Pagi hari minum teh di beranda,\nDitemani suara burung berkicau~\nSemoga :name selalu bahagia,\nDan cita-citanya segera terwujud!\"\n\n\"Pergi ke sawah panen padi,\nBekerja keras dari pagi-pagi~\nIlmu yang kamu pelajari hari ini,\nAkan jadi bekalmu nanti!\"\n\n🌙 Doa kami: Ya Allah, berikanlah kesehatan, kecerdasan, dan keberkahan untuk :name. Jadikanlah ia anak yang berguna bagi keluarga, masyarakat, dan bangsa. Aamiin. 🤲\n\nTerus berjuang ya! Perjalananmu masih panjang dan penuh keindahan! 💫",

        // 15 — Doa 7 Poin + Pantun
        "🎂 Selamat Ulang Tahun, :name!\n\nHari ini kami mendoakan 7 hal untukmu:\n1. 💪 Kesehatan yang selalu terjaga\n2. 🧠 Kecerdasan yang terus berkembang\n3. 🙏 Hati yang selalu bersyukur\n4. 🌟 Rezeki yang halal dan berkah\n5. 👨‍👩‍👧 Keluarga yang selalu harmonis\n6. 🎯 Cita-cita yang segera terwujud\n7. 😊 Kebahagiaan yang nggak ada habisnya!\n\nPantun penutup:\n\"Buah rambutan merah merona, dijual di pasar pagi-pagi~\nJadilah anak yang bijaksana, agar kelak hidupmu penuh berkahi!\"\n\nHappy birthday, :name! Kamu istimewa! ⭐",
    ];

    public function getMessage(Student $student): string
    {
        $template = $this->templates[array_rand($this->templates)];
        $firstName = explode(' ', trim($student->user->name))[0];
        return str_replace(':name', $firstName, $template);
    }
}
