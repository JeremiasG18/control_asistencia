<section>

    <h2>Celugrama</h2>
    <form action="index.php" class="formFile" method="post" enctype="multipart/form-data">
        <input type="hidden" name="accion" value="uploadfile">
        <label for="file">Inserte el archivo excel</label>
        <input type="file" name="file" id="file" accept=".xls, .xlsx" required>
        <input type="submit" value="Enviar">
    </form>

</section>

<section>
    <select id="asistents">
        <option value="0">Seleccione una planilla</option>
    </select>
    <div class="asistents">
        <h3>Asistentes</h3>
    </div>

</section>