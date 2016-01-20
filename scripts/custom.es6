
function parse(resp) {
	if (resp.ok) {
		let type = resp.headers.get('Content-Type');
		switch (type) {
			case 'application/json':
				return resp.json()
				break;

			case 'application/xml':
			case 'text/xml':
				return resp.text().then(xml => DOMParser.parseFromString(xml, application/xml));

			case 'text/html':
				return resp.text().then(html => DOMParser.parseFromString(html, application/html));
				break;

			case 'image/svg':
				return resp.text().then(svg => DOMParser.parseFromString(svg, application/xml));
				break;

			case 'text/plain':
				return resp.text();
				break;
		}
	}
}
(() => {
	self.addEventListener('load', () => {
		['details', 'dialog', 'video', 'audio', 'canvas', 'svg'].forEach(feature => {
			supports(feature)
				? document.documentElement.classList.add(feature)
				: document.documentElement.classList.add(`no-${feature}`);
		})
		$('details > summary').click((event) => {
			event.target.parentElement.hasAttribute('open')
				? event.target.parentElement.removeAttribute('open')
				: event.target.parentElement.setAttribute('open', '');
		})
		$('a').filter(a => a.origin === location.origin).click(event => {
			event.preventDefault();
			let url = new URL(event.target.href);
			let headers = new Headers();

			headers.set('Accept', 'application/json');
			fetch(url, {
				headers,
				method: 'GET',
				credentials: 'omit',
				mode: 'same-origin'
			}).then(parse).then(handleJSON).catch(error => console.error(error));
		});
	});
})();
