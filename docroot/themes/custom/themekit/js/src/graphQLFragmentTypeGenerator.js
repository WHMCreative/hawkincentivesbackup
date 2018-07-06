const fetch = require('node-fetch');
const fs = require('fs');



fetch('http://bhk-d8.drupalvm/graphql', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'X-Frame-Options': 'SameOrigin'
  },
  body: JSON.stringify({
    query: `
    {
      __schema {
        types {
          kind
          name
          possibleTypes {
            name
          }
        }
      }
    }
  `,
  }),
})
  .then(result => result.json())
  .then(result => {
    // here we're filtering out any type information unrelated to unions or interfaces
    const filteredData = result.data.__schema.types.filter(
      type => type.possibleTypes !== null
    );
    result.data.__schema.types = filteredData;
    // console.log(JSON.stringify(result.data));
    fs.writeFile('./fragmentTypes.json', JSON.stringify(result.data));
  });
