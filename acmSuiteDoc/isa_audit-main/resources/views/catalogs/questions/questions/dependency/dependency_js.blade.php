<script>
/**
 * Open Requirements selection view
 */
function openDependency(idQuestion) {
  $('.questions').addClass('d-none');
  $('.loading').removeClass('d-none');
  setData(idQuestion)
  .then(data => {
    $('.showDependency').removeClass('d-none');
    $('.loading').addClass('d-none');
  })
  .catch(e => {
    console.error(e);
    $('.loading').addClass('d-none');
    $('.questions').removeClass('d-none');
    toastAlert('No se pudo cargar la lista de dependencia', 'error');
  });  
}

  /**
 * Open Requirements selection view
 */
function closeDependency() {
  try {
    $('.showDependency').addClass('d-none');
    $('.loading').removeClass('d-none');
    setTimeout(() => {
      $('.questions').removeClass('d-none');
      $('.loading').addClass('d-none');
    }, 1000);
  } catch (error) {
    setTimeout(() => {
      $('.questions').removeClass('d-none');
      $('.loading').addClass('d-none');
    }, 1000);
  }
}

function getAnswers(idQuestion){
  return new Promise((resolve, reject) => {
    $.get('/catalogs/questions/dependency/index', {
      _token: document.querySelector('meta[name="csrf-token"]').content,
      idQuestion: idQuestion
    },
    (data) => {
      const { question, questionsPool } = data;
      const { answers } = question;
      setDataQuestion(question)
      // Answers nodes      
      let newNodes = [];
      const openNode = (questionsPool.length != 0) ? true : false;
      answers.forEach(a => {
        let answerNode = {
          id: `a${a.id_answer_question}q0`,
          text: a.description,
          params : {
            id: `a${a.id_answer_question}q0`,
            id_answer_question: a.id_answer_question,
            id_question: null,
          },
          selected: (questionsPool.length == a.block.length),
          children: buildChilds(a.id_answer_question, questionsPool, a.block)
        };
        newNodes.push(answerNode);
      });
      resolve(newNodes);
    })
    .fail(e => {
      reject(e.statusText)
    });
  })
}

function setDataQuestion(data) {
  const { question, matter, aspect, type } = data;
  document.querySelector('.questionTitle').innerHTML = question;
  document.querySelector('.matterTitle').innerHTML = matter.matter;
  document.querySelector('.aspectTitle').innerHTML = aspect.aspect;
  document.querySelector('.typeTitle').innerHTML = type.question_type;
}

function buildChilds(idAnswer, questionsPool, block) {
  let newQuestions = [];
  questionsPool.forEach(q => {
    const selectedNode = (block.filter(d => q.id_question == d.id_question) != 0) ? true : false;
    let questionNode = { 
      id: `a${idAnswer}q${q.id_question}`,
      text: `${q.order} - ${q.question}`,
      params : {
        id: `a${idAnswer}q${q.id_question}`,
        id_answer_question: idAnswer,
        id_question: q.id_question,
      },
      selected: selectedNode
    }
    newQuestions.push(questionNode);
  });
  return newQuestions;
}

function setData(idQuestion) {
  return new Promise((resolve, reject) => {
    getAnswers(idQuestion)
    .then(nodes => {
      $('#dependencyTreeView').find('div').remove();
      let html = `<div class="panel-group">`;
      if (nodes.length > 0) {
        nodes.forEach(n => {
          html += `
            <div class="panel panel-default">
              <div class="panel-heading">
                <a data-toggle="collapse" href="#${n.id}">
                  <h4 class="panel-title font-weight-bold" style="color:#212529;text-decoration: underline;">
                    Respuesta: ${n.text}
                  </h4>
                </a>
              </div>
          `;
          html += `
              <div id="${n.id}" class="panel-collapse collapse show">
                <ul class="list-group">
            `;
          if (n.children.length > 0) {
            n.children.forEach(c => {
              const { id, id_answer_question, id_question } = c.params;
              html += `
              <a onclick="setDependencyAction('#${id}', ${id_answer_question}, ${id_question})">
                <li id="${c.id}" selected="${c.selected}" style="cursor:pointer;" 
                  class="list-group-item list-group-item-${ c.selected ? 'success' : 'secondary'}"
                >
                  ${c.text}
                </li>
              </a>
              `;
            });
          }
          else {
            html += `<li class="list-group-item list-group-item-secondary">
                Esta pregunta es la última del formulario, no contiene preguntas para definir dependencia
              </li>`;
          }
          html += `
                </ul>
              </div>
            `;
        });
      }
      else {
        html += `<h4 class="panel-title font-weight-bold" style="color:#212529;">
                  Esta pregunta no contiene respuestas para definir dependencia, agrega más en la sección respuestas <i class="fa fa-check-square-o fa-lg"></i>
                </h4>`;
      }
      html += '</div>';
      document.getElementById('dependencyTreeView').insertAdjacentHTML('beforeend', html);
      resolve(true)
    })
    .catch(e => {
      reject(e)
    });
  });
}

function setDependencyAction(id, id_answer_question, id_question) {
  const element = document.querySelector(id);
  const action = element.getAttribute('selected');
  if (action == 'false') {
    $('.loading').removeClass('d-none');
    setDependency(id_answer_question, id_question)
    .then(data => {
      element.classList.remove('list-group-item-secondary');
      element.classList.add('list-group-item-success');
      element.setAttribute('selected', 'true');
      $('.loading').addClass('d-none');
      toastAlert(data.msg, data.status);
    })
    .catch(e => {
      console.error(e);
      $('.loading').addClass('d-none');
      toastAlert('Error al procesar petición', 'error');
    });
  }
  else {
    $('.loading').removeClass('d-none');
    removeDependency(id_answer_question, id_question)
    .then(data => {
      element.classList.remove('list-group-item-success');
      element.classList.add('list-group-item-secondary');
      element.setAttribute('selected', 'false');
      $('.loading').addClass('d-none');
      toastAlert(data.msg, data.status);
    })
    .catch(e => {
      console.error(e);
      $('.loading').addClass('d-none');
      toastAlert('Error al procesar petición', 'error');
    });
  }
}

function handleClass(element, removeClass, addClass) {
  element.classList.remove(removeClass);
  element.classList.addClass(addClass);
}

function setDependency(id_answer_question, id_question) {
  return new Promise((resolve, reject) => {
    $.post('/catalogs/questions/dependency/set', {
      _token: document.querySelector('meta[name="csrf-token"]').content,
      id_question: id_question,
      id_answer_question: id_answer_question
    },
    (data) => {
      resolve(data);
    })
    .fail(e => {
      reject(e.statusText)
    });
  });
}

function removeDependency(id_answer_question, id_question) {
  return new Promise((resolve, reject) => {
    $.post('/catalogs/questions/dependency/remove', {
      _token: document.querySelector('meta[name="csrf-token"]').content,
      id_question: id_question,
      id_answer_question: id_answer_question
    },
    (data) => {
      resolve(data);
    })
    .fail(e => {
      reject(e.statusText)
    });    
  });
}

</script>