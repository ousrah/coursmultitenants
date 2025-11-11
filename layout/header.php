<!DOCTYPE html>
<html lang="<?= $lang ?>"> <!-- Langue dynamique -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(COURSE_TITLE) ?></title>
    <!-- Les scripts et styles restent les mêmes -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-6RRKWY18MK"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-6RRKWY18MK');
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
   <link  href="style.css" rel="stylesheet">
</head>
<body class="antialiased" id="page-top">
<main class="max-w-5xl mx-auto p-4 sm:p-6 lg:p-8">

    <!-- En-tête de la page -->
    <div class="flex justify-center my-8">
        <a href = "https://ousrah.portal-edu.com/cv/">
            <div class="flex items-center space-x-4">
                <img src="me2.png" alt="Photo de F. Rahmouni Oussama" class="w-24 h-24 rounded-full border-2 border-gray-300 p-px">
                <div>
                    <!-- Texte traduit -->
                    <p class="font-bold text-lg text-gray-800"><?= htmlspecialchars($strings['author_byline']) ?></p>
                    <p class="text-sm text-gray-600"><?= htmlspecialchars($strings['author_title']) ?></p>
                </div>
            </div>
        </a>
    </div>

    <div class="mb-12">
        <h1 class="text-3xl md:text-5xl font-extrabold text-gray-900 text-center"><?= htmlspecialchars(COURSE_TITLE) ?></h1>
        <!-- Texte traduit -->
        <h3 class="pt-4 text-md md:text-md font-extrabold text-gray-500 text-right"><?= htmlspecialchars($strings['last_update']) ?>: <?= htmlspecialchars(COURSE_LAST_UPDATE) ?></h3>
        
        <div class="flex justify-center my-8">
             <!-- ... (Contenu de la bio de l'auteur) ... -->
        </div>

        <!-- SOMMAIRE DYNAMIQUE -->
        <div class="bg-white rounded-lg shadow-sm border p-6 my-8">
            <!-- Texte traduit -->
            <h3 class="text-xl font-bold text-gray-800 mb-4"><?= htmlspecialchars($strings['toc_title']) ?></h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 text-gray-700">
                <?php
                $parts_count = count($course_parts);
                $half_point = ceil($parts_count / 2);
                $i = 0;
                ?>

                <!-- Colonne 1 -->
                <div class="space-y-4">
                <?php foreach ($course_parts as $part_title => $chapters): ?>
                    <?php if ($i == $half_point): ?>
                        </div><!-- Fin Colonne 1 -->
                        <!-- Colonne 2 -->
                        <div class="space-y-4 mt-4 md:mt-0">
                    <?php endif; ?>
                    
                    <div>
                        <h4 class="font-semibold text-gray-900"><?= htmlspecialchars($part_title) ?></h4>
                        <ul class="list-disc ml-6 text-sm text-gray-600 space-y-1 mt-1">
                            <?php foreach ($chapters as $chapter): ?>
                                <li><a href="#<?= $chapter['id'] ?>" class="hover:underline"><?= htmlspecialchars($chapter['title']) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php $i++; endforeach; ?>
                  <div>
                  <!-- Texte traduit -->
                  <a href="#conclusion"><h4 class="font-semibold text-gray-900"><?= htmlspecialchars($strings['conclusion_link']) ?></h4></a>
                  </div>
                </div><!-- Fin Colonne 2 -->
            </div>
        </div>
    </div>