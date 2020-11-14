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
