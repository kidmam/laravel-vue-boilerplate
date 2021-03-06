import Vue from 'vue';
import {
  mount,
} from 'vue-test-utils';
import faker from 'faker';

import Home from '@/pages/Home.vue';
import storeMock from '../mocks/store-mock';
import configStore from '../mocks/config-store';

const localState = {
  homePath: '/',
  user: {
    name: faker.name.findName(),
    type_id: 1,
  },
  homeItems: [{
    name: faker.lorem.word(),
    link: faker.internet.url(),
    icon: faker.lorem.word(),
  }, {
    name: faker.lorem.word(),
    link: faker.internet.url(),
    icon: faker.lorem.word(),
  }, {
    name: faker.lorem.word(),
    link: faker.internet.url(),
    icon: faker.lorem.word(),
  }],
};

storeMock.modules.Root.state = localState;

describe('Home.vue', () => {
  const store = configStore(Vue, storeMock);

  it('should have 3 CardHome components and has a name with "Welcome" on the title', () => {
    const wrapper = mount(Home, {
      store,
    });

    const welcome = 'Welcome';

    expect(wrapper.find('h1').text()).toEqual(`${welcome}, ${localState.user.name}`);
    expect(wrapper.findAll('.card-home')).toHaveLength(localState.homeItems.length);
  });

  it('should have a name with "Bem-vindo" on the title', () => {
    Vue.i18n.set('pt');

    const wrapper = mount(Home, {
      store,
    });

    const welcome = 'Bem-vindo';

    expect(wrapper.find('h1').text()).toEqual(`${welcome}, ${localState.user.name}`);
  });
});
