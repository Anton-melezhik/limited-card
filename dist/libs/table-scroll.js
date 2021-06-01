addEventListener('load', e => {

  const tableScroll = document.querySelector('.table-scroll');
  const tablePlaceholder = document.querySelector('.table-placeholder');

  const tableButtons = tableScroll.querySelectorAll('.table-scroll img');

  [tableScroll, tablePlaceholder].forEach(element => {
    element.classList.toggle('state-hidden');
  });

  tableButtons.forEach(button => {
    button.style.top = tableScroll.clientHeight < innerHeight ? `${tableScroll.clientHeight / 2}px` : '350px'
  })

  const offsetTrigger = ($parent, { $childrens = false, start = 'element', enter = false, leave = false } = {}) => {
    $parent = typeof $parent === 'string' ? document.querySelector($parent) : $parent;
    const parentRect = $parent.getBoundingClientRect();
    const parentOffset = parentRect.top;
    const parentArea = parentRect.bottom;
    const scrollHandler = $el => {
      addEventListener('scroll', e => {
        const heightOffset = pageYOffset + (start === 'element' ? innerHeight : 0);
        if (heightOffset >= parentOffset && heightOffset <= parentArea) {
          enter && enter($el, heightOffset - parentOffset);
        } else {
          leave && leave($el);
        }
      });
    };
    if (!$childrens) {
      scrollHandler($parent);
    } else {
      $childrens = typeof $childrens === 'string' ?
        document.querySelectorAll($childrens) :
        $childrens;
      $childrens.forEach($children => {
        scrollHandler($children);
      });
    }
  };

  var tables = document.querySelectorAll('.limited-item');
  var firstTableRows = tables[0].querySelectorAll('tbody > tr');
  var secondTableRows = tables[1].querySelectorAll('tbody > tr');

  tables[0].querySelectorAll('thead td').forEach(cell => {
    const secondThead = tables[1].querySelector('thead');
    cell.style.height = secondThead.offsetHeight - 14 + 'px';
  });

  secondTableRows.forEach((secondTableRow, secondTableIndex) => {
    var firstTableCell = firstTableRows[secondTableIndex].querySelector('td');
    var secondTableCells = secondTableRow.querySelectorAll('td');
    secondTableCells.forEach((cell, index) => {
      if (index === 0 || cell.className.indexOf('row-head-hide') !== -1) {
        cell.style.height = firstTableCell.offsetHeight + 'px';
      }
    });
  });

  const scrollTriggers = (triggers, target) => {
    triggers = document.querySelectorAll(triggers);
    target = document.querySelector(target);
    let step = 0;
    let count = 0;
    const targetSize = target.offsetWidth - target.scrollLeft;
    triggers.forEach(trigger => {
      const type = trigger.getAttribute('data-type');
      target.addEventListener('scroll', e => {
        if (target.scrollLeft >= trigger.offsetWidth && type === 'prev') {
          if (trigger.className.indexOf('state-active') === -1) {
            trigger.classList.add('state-active');
          }
        } else if ((target.scrollLeft + target.offsetWidth) !== target.scrollWidth && type === 'next') {
          if (trigger.className.indexOf('state-active') === -1) {
            trigger.classList.add('state-active');
          }
        } else {
          if (trigger.className.indexOf('state-active') !== -1) {
            trigger.classList.remove('state-active');
          }
        }
      });
      trigger.addEventListener('click', e => {
        count += 1;
        if (step < 0) {
          step = 0;
        } else if (step > target.scrollWidth) {
          step -= targetSize;
        }
        target.scroll(type === 'next' ? (step += targetSize) : (step -= targetSize), 0);
      });
    });
  };

  scrollTriggers('.table-scroll__button', '.table-scroll_scrolled');

  offsetTrigger('.table-scroll', {
    $childrens: '.table-scroll thead',
    start: 'page',
    enter($el, offset) {
      if (tableScroll.clientHeight > innerHeight) {
        $el.style.transform = 'translateY(' + offset + 'px)';
      }
    },
    leave($el) {
      $el.style.transform = 'translateY(0)';
    }
  });

  offsetTrigger('.table-scroll', {
    $childrens: '.table-scroll__button img',
    start: 'element',
    enter($el, offset) {
      if (offset - (innerHeight / 2) > (innerHeight / 2)) {
        $el.style.top = offset - (innerHeight / 2) + 'px';
      }
    }
  });

});