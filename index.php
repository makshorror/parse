<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta
            name="viewport"
            content="width=device-width"
    >
    <meta
            http-equiv="X-UA-Compatible"
            content="ie=edge"
    >
    <title>Парсер</title>

    <link
            rel="stylesheet"
            href="assets/styles/style.css"
    >
</head>
<body>
<section class="container">
    <div class="wrapper">
        <form
                class="form"
                method="POST"
        >
            <h3 class="heading">Введите артикул или диапазон артикулов</h3>
            <div class="articles">

                <div class="row">
                    <label for="minArticle">От: </label>
                    <input
                            class="input"
                            id="minArticle"
                            type="text"
                            name="minArticle"
                            placeholder="Введите артикул/Минимальный артикул..."
                    >
                </div>
                <div class="row">
                    <label for="maxArticle">До: </label>
                    <input
                            class="input"
                            id="maxArticle"
                            type="text"
                            name="maxArticle"
                            placeholder="Введите артикул/Максимальный артикул..."
                    >
                </div>
            </div>
            <div class="col">
                <label for="productName">Название продукта: </label>
                <input
                        class="input"
                        id="productName"
                        type="text"
                        name="productName"
                        placeholder="Введите название продукта..."
                >
            </div>
            <div class="price">


                <h3 class="heading">Введите диапазон цен</h3>
                <div class="row">
                    <label for="minPrice">От: </label>
                    <input
                            class="input"
                            id="minPrice"
                            type="number"
                            name="minPrice"
                            placeholder="Введите минимальную сумму..."
                    >
                </div>
                <div class="row">
                    <label for="maxPrice">До: </label>
                    <input
                            class="input"
                            id="maxPrice"
                            type="number"
                            name="maxPrice"
                            placeholder="Введите максимальную сумму..."
                            max="42550"
                    >
                </div>
            </div>
            <div class="col">
                <label for="limit">Лимит строк: </label>
                <input
                        class="input"
                        id="limit"
                        type="number"
                        name="limit"
                        placeholder="Введите лимит строк..."
                        min="0"
                >
                <button
                        class="btn"
                        type="submit"
                        name="parse"
                >ПАРСИТЬ
                </button>
                <form
                        method="POST">
                    <button
                            class="btn-parse"
                            type="submit"
                            name="backToResult"
                    >Вернуться к результатам парсинга
                    </button>
                </form>
            </div>

        </form>
    </div>
    <?php
    require 'classes/database.php';
    require 'classes/parsing.php';


    $database = new Database();
    $parsing = new Parsing();

    if (isset($_POST['backToResult'])) {
        $database->databaseConnect();
        $database->createTable();
        $database->databaseConnectClose();
        echo '<script>location.href="result.php"</script>';
    }

    if (isset($_POST['parse'])) {
        $database->databaseConnect();
//Удаление таблицы из БД
        $database->dropTable();

//Создание таблицы в БД
        $database->createTable();


        $parsing->parseFile();

        if ($_POST["minArticle"] !== "" || $_POST["maxArticle"] !== "") {
            $parsing->sortByArticle(trim($_POST["minArticle"]), trim($_POST["maxArticle"]));
        }

        if ($_POST["productName"] != "") {
            $parsing->sortByProductName(trim($_POST["productName"]));
        }

        if ($_POST["minPrice"] !== "" || $_POST["maxPrice"] !== "") {
            $parsing->sortByPrice($_POST["minPrice"], $_POST["maxPrice"]);
        }

        if ($_POST["limit"] !== "") {
            $parsing->limitation($_POST["limit"]);
        }

//Пуш финального парсинга в БД
        $database->pushInDB($parsing->data);

//Закрываем соединение
        $database->databaseConnectClose();
        echo '<script>location.href="result.php"</script>';
    }
    ?>
</section>
</body>
</html>