class DataSrcSet {
  constructor(elements) {
    this.$ = elements;
    this.collection = [];
    this.currentMediaIndex = 0;
  }

  init() {
    let countLimit = this.$.length - 1;
    let count = 0;

    [].map.call(this.$, item => {
      this.collection.push(
        new Box({
          $: item,
          media: parseMedia(item.dataset.srcset)
        })
      );

      if (count !== countLimit) {
        count++;
      } else {
        this.observe();
      }
    });
  }

  transform() {
    this.collection.map(item => {
      let media = item.media;

      for (let i = 0; i < media.length; i++) {
        if (window.innerWidth <= media[i][0]) {
          this.currentMediaIndex = i;
        }
      }
      this.setSource();
    });
  }

  observe() {
    this.transform();
    window.addEventListener('resize', this.transform.bind(this));
  }

  setSource() {
    this.collection.map(item => {
      try {
        item.$.src = item.media[this.currentMediaIndex][1];
      } catch (er) {
        new Error(er);
      }
    });
  }
}

function parseMedia(string) {
  const array_A = string.split(', ');
  const array_B = [];

  if (!array_A.length || array_A.length === 0) return false;

  for (let i = 0; i < array_A.length; i++) {
    let temp = array_A[i].split(' ');
    array_B.push([+temp[1].replace('w', ''), temp[0]]);
  }
  return array_B;
}

class Box {
  constructor(options) {
    this.$ = options.$;
    this.media = options.media;
    return this;
  }
}

module.exports = DataSrcSet;
