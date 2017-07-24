import { Angular2HttpPage } from './app.po';

describe('angular2-http App', () => {
  let page: Angular2HttpPage;

  beforeEach(() => {
    page = new Angular2HttpPage();
  });

  it('should display welcome message', () => {
    page.navigateTo();
    expect(page.getParagraphText()).toEqual('Welcome to app!');
  });
});
