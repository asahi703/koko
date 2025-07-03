document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const tooltip = document.getElementById('tooltip');

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'ja',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: ''
        },
        eventMaxStack: true, // イベントが多いとき自動で「＋もっと見る」にしてくれる


        events: [
            {
                title: '入学式',
                start: '2025-04-08',
                description: '体育館にて午前10時から'
            },
            {
                title: '運動会',
                start: '2025-05-20',
                description: '校庭で開催（雨天中止）'
            }
        ],

        dateClick: function (info) {
            // モーダル表示 & 日付セット
            const modal = new bootstrap.Modal(document.getElementById('eventModal'));
            // モーダル内の全ての event_date input に値をセット
            document.querySelectorAll('#eventModal input[name="event_date"]').forEach(function (input) {
                input.value = info.dateStr;
            });
            modal.show();
        },

        eventContent: function (arg) {
            // カレンダーの枠内にタイトルと説明を表示
            let arrayOfDomNodes = [];
            let titleEl = document.createElement('div');
            titleEl.innerHTML = arg.event.title;
            arrayOfDomNodes.push(titleEl);

            if (arg.event.extendedProps.description) {
                let descEl = document.createElement('div');
                descEl.innerHTML = '<small>' + arg.event.extendedProps.description + '</small>';
                descEl.style.fontSize = '0.8em';
                descEl.style.color = '#666';
                arrayOfDomNodes.push(descEl);
            }
            return { domNodes: arrayOfDomNodes };
        },

        eventDidMount: function (info) {
            // ツールチップ表示処理
            info.el.addEventListener('mouseenter', function (e) {
                // タイトルと説明を両方表示
                const title = info.event.title;
                const desc = info.event.extendedProps.description || '詳細なし';
                tooltip.innerHTML = `<strong>${title}</strong><br><small>${desc}</small>`;
                tooltip.style.display = 'block';
                tooltip.style.left = e.pageX + 10 + 'px';
                tooltip.style.top = e.pageY + 10 + 'px';
            });
            info.el.addEventListener('mousemove', function (e) {
                tooltip.style.left = e.pageX + 10 + 'px';
                tooltip.style.top = e.pageY + 10 + 'px';
            });
            info.el.addEventListener('mouseleave', function () {
                tooltip.style.display = 'none';
            });
        }
    });
    calendar.render();
});