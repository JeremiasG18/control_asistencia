function show(data){
    const select = document.getElementById('forms');
    select.innerHTML = '';
    select.innerHTML = '<option value="0">Seleccione una planilla</option>';
    data.forEach(datos => {
        const option = document.createElement('option');
        option.value = datos.id;
        option.textContent = `Lider: ${datos.name_file} Reunion: ${datos.type_meet}`;
        select.appendChild(option);
    });
    select.addEventListener('change', () => {
        const selectedOption = select.options[select.selectedIndex];
        const idSeleccionado = selectedOption.value;
        fetch(`index.php?file=${idSeleccionado}`)
        .then(response => response.json())
        .then(data => {
            showAsistents(data);
        })
        .catch(error => {
            console.error(error);
        });
    });
    return select;
}

// Mostrar asistentes
function showAsistents(data){ 

    const inputHidden = document.querySelector('.idForm');
    inputHidden.setAttribute('value', data[0].id);
    const tbody = document.querySelector('.asistents');

    tbody.innerHTML = '';

    for (let i = 0; i < data.length; i++) {
        const tr = document.createElement('tr');
        if (i != 0) {
            tr.innerHTML = `
                <td><input type="text" name="asistentes[${i}][nombre]" value="${data[i].asistente}"></td>
                <td><input type="date" name="asistentes[${i}][fecha]" value="${
                    data[i].fecha_nacimiento != null ? data[i].fecha_nacimiento : ''
                }"></td>
            `;

            tr.innerHTML += 
            `
                <td>
                    ${
                        data[i].bautismo == 'si'
                        ? '<input type="checkbox" name="asistentes[' + i + '][bautismo]" checked>' 
                        : '<input type="checkbox" name="asistentes[' + i + '][bautismo]">'
                    }
                </td>

                <td>
                    ${
                        data[i].encuentro == 'si'
                        ? '<input type="checkbox" name="asistentes[' + i + '][encuentro]" checked>' 
                        : '<input type="checkbox" name="asistentes[' + i + '][encuentro]">'
                    }
                </td>
                <td>
                    ${
                        data[i].abc == 'si'
                        ? '<input type="checkbox" name="asistentes[' + i + '][abc]" checked>' 
                        : '<input type="checkbox" name="asistentes[' + i + '][abc]">'
                    }
                </td>
                <td>
                    ${
                        data[i].nivel1 == 'si'
                        ? '<input type="checkbox" name="asistentes[' + i + '][nivel1]" checked>' 
                        : '<input type="checkbox" name="asistentes[' + i + '][nivel1]">'
                    }
                </td>
                <td>
                    ${
                        data[i].nivel2 == 'si'
                        ? '<input type="checkbox" name="asistentes[' + i + '][nivel2]" checked>' 
                        : '<input type="checkbox" name="asistentes[' + i + '][nivel2]">'
                    }
                </td>
                <td>
                    ${
                        data[i].mentores == 'si'
                        ? '<input type="checkbox" name="asistentes[' + i + '][mentores]" checked>'
                        : '<input type="checkbox" name="asistentes[' + i + '][mentores]">'
                    }
                </td>
            `;
            tbody.appendChild(tr);
            continue;
        }

    }

    return tbody;
}

document.addEventListener('DOMContentLoaded', () => {

    // listar plantillas cargadas
    fetch('index.php?accion=showforms')
    .then(response => response.json())
    .then(data => {
        show(data);
    })
    .catch(error => {
        console.error(error);  
    })

    // subir plantilla nueva
    const formFile = document.querySelector('.formFile');
    formFile.addEventListener('submit', (e) =>{
        e.preventDefault();
        let confirm = window.confirm('Desea enviar el formulario?');
        if (confirm) {
            const formData = new FormData(formFile);
            fetch(formFile.action,{
                method: formFile.method,
                body: formData
            })
            .then(response => response.json())
            .then(data =>{
                if (data = 'sas'){
                    console.log(data);
                }else{
                    show(data)
                }
            })
            .catch(error =>{
                console.error('Error del servidor o de una mala solicitud: ' + error);
            });
        }
    });

    const btnAddAsistent = document.querySelector('.btnAddAsistent');
    btnAddAsistent.addEventListener('click', ()=>{
        addAsistent();
    });

    const btnSaveAsistent = document.querySelector('.fmSaveAsistents');
    btnSaveAsistent.addEventListener('submit', (e)=>{
        e.preventDefault();
        formData = new FormData(btnSaveAsistent);
        fetch(btnSaveAsistent.action,{
            method: btnSaveAsistent.method,
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log(data);
        })
        .catch(error => {
            console.error(error);
        });
    });

});

// Agregar formulario para nuevos asistentes
function addAsistent(){
    const tbody = document.querySelector('.asistents');

    let countRow = tbody.getElementsByTagName('tr').length + 1;

    tbody.innerHTML += `

        <tr>
            <td><input type="text" name="[${countRow}][nombre]"></td>
            <td><input type="date" name="[${countRow}][fecha]"></td>
            <td><input type="checkbox" name="[${countRow}][bautismo]"></td>
            <td><input type="checkbox" name="[${countRow}][encuentro]"></td>
            <td><input type="checkbox" name="[${countRow}][abc]"></td>
            <td><input type="checkbox" name="[${countRow}][nivel1]"></td>
            <td><input type="checkbox" name="[${countRow}][nivel2]"></td>
            <td><input type="checkbox" name="[${countRow}][mentores]"></td>
        </tr>

    `;

    return tbody;
    
}