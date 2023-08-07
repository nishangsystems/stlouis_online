<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form method="post" style="border: 1pz solid black; width: fit-content; margin-inline: auto;" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" style="height: 2rem; margin-block: 1rem; border-radius: 3px; border: 1px solid black; display: block;">
        <input type="submit" value="Submit" style="height: 2rem; border-radius: 3px; border: 1px solid black; display: block;">
    </form>
</body>
</html>