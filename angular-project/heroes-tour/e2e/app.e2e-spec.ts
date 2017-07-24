import { RespPage } from './app.po';

describe('resp App', () => {
  let page: RespPage;

  beforeEach(() => {
    page = new RespPage();
  });

  it('should display welcome message', () => {
    page.navigateTo();
    expect(page.getParagraphText()).toEqual('Welcome to app!');
  });
});
