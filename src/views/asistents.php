<section>

    <h2>Celugrama</h2>
    <form action="index.php" class="formFile" method="post" enctype="multipart/form-data">
        <input type="hidden" name="accion" value="uploadfile">
        <label for="file">Inserte el archivo excel</label>
        <input type="file" name="file" id="file" accept=".xls, .xlsx" required>
        <input type="submit" value="Enviar">
    </form>

</section>

<section class="forms_control">
    <select id="forms">
        <option value="0">Seleccione una planilla</option>
    </select>
    <form action="index.php" method="POST" class="fmSaveAsistents">
        <input type="hidden" name="accion" value="saveAsistents">
        <input type="hidden" name="id" class="idForm">
        <table border="1">
            <thead>
                <tr>
                    <td colspan="9">Asistentes</td>
                </tr>
                <tr>
                    <td>Nombre y Apellido</td>
                    <td>Fecha de Nacimiento</td>
                    <td class="step">Bautismo</td>
                    <td class="step">Encuentro</td>
                    <td class="step">ABC</td>
                    <td class="step">Nivel 1</td>
                    <td class="step">Nivel 2</td>
                    <td class="step">Mentores</td>
                    <td>Accion</td>
                </tr>
            </thead>
            <tbody class="asistents">
                <tr><td colspan="9">No se ha seleccionado una planilla</td></tr>
            </tbody>
        </table>
        <input type="button" value="Agregar Asistente" class="btnAddAsistent">
        <input type="submit" value="Guardar">
    </form>

</section>