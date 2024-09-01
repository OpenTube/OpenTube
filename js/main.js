document.querySelectorAll('video').forEach((video) => {
  // desktop
  video.addEventListener('mouseover', (e) => {
    const hoverThumbnail = e.target.getAttribute('poster').replace(/.png/g, '.gif')
    e.target.setAttribute('poster', hoverThumbnail)
  })
  video.addEventListener('mouseout', (e) => {
    const hoverThumbnail = e.target.getAttribute('poster').replace(/.gif/g, '.png')
    e.target.setAttribute('poster', hoverThumbnail)
  })
  // mobile
  video.addEventListener('touchstart', (e) => {
    const hoverThumbnail = e.target.getAttribute('poster').replace(/.png/g, '.gif')
    e.target.setAttribute('poster', hoverThumbnail)
  })
  video.addEventListener('touchend', (e) => {
    const hoverThumbnail = e.target.getAttribute('poster').replace(/.gif/g, '.png')
    e.target.setAttribute('poster', hoverThumbnail)
  })
})

document.addEventListener('keydown', (event) => {
  if (event.target.type === 'text') {
    // no key bindings in input fields
    return
  }
  const params = new URLSearchParams(document.location.search)
  console.log(params)
  let newPage = 0
  if (event.key === 'f') {
    const videoDom = document.querySelector('.video-main')
    if(videoDom) {
      if(videoDom.classList.contains('fullscreen')) {
        document.exitFullscreen()
      } else {
        if (videoDom.requestFullscreen) {
          videoDom.requestFullscreen()
        } else if (videoDom.webkitRequestFullscreen) {
          videoDom.webkitRequestFullscreen()
        } else if (videoDom.msRequestFullScreen) {
          videoDom.msRequestFullScreen()
        }
      }
      videoDom.classList.toggle('fullscreen')
    }
  }
  if (event.key === 'n') {
    newPage = parseInt(params.get('p'), 10) + 1
  }
  if (event.key === 'N') { // event.shiftKey
    newPage = parseInt(params.get('p'), 10) - 1
  }
  if (event.key === 'n' || event.key === 'N') {
    const lastPage = parseInt(document.querySelector('.pages a:last-child').innerHTML, 10)
    if (isNaN(newPage) || newPage === 'NaN' || (!newPage && newPage !== 0)) {
      newPage = 1
    }
    if (newPage < 0) {
      newPage = 0
    }
    if (newPage >= lastPage) {
      newPage = lastPage
    }
    params.set('p', newPage)
    const newUrl = document.location.toString().split('?')[0] + '?' + params.toString()
    console.log(newUrl)
    window.location = newUrl
  }
})
