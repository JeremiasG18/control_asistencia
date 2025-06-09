function show(data){
    const contentAsistents = document.querySelector('.asistents');
    const select = document.getElementById('asistents');
    select.innerHTML = '';
    select.innerHTML = '<option value="0">Seleccione una planilla</option>';
    data.forEach(datos => {
        const option = document.createElement('option');
        option.value = datos.id;
        option.textContent = `Lider: ${datos.name_file} Reunion: ${datos.type_meet}`;
        select.appendChild(option);
    });
    contentAsistents.append(select);

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
    return contentAsistents;
}

function showAsistents(data){    
    const div = document.createElement('div');
    div.setAttribute('class', 'asistents-list');
    div.innerHTML = '';
    for (let i = 0; i < data.length; i++) {
        for (let j = 0; j < data[i].length; j++) {
            const form = document.createElement('form');
            form.innerHTML += `
                <input type="text" value="${data[i][j]}">
            `;
            div.appendChild(form);
        }
        
    }

    let asistents = document.querySelector('.asistents');
    if (asistents.querySelector('.asistents-list') == null) {
        return asistents.appendChild(div);  
    }
    asistents.querySelector('.asistents-list').remove();
    return asistents.appendChild(div);
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
                show(data)
            })
            .catch(error =>{
                console.error('Error del servidor o de una mala solicitud: ' + error);
            });
        }
    });

})