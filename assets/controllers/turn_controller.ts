import { Controller } from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['dice', 'trade', 'endTurn', 'diceOne', 'diceTwo', 'giveUp']
    static values = {
      enabled: Boolean,
      diceOne: Number,
      diceTwo: Number,
      gameCode: Number,
      playerId: Number,
      position: Number,
    }
    declare diceTarget: HTMLDivElement;
    declare tradeTarget: HTMLDivElement;
    declare endTurnTarget: HTMLDivElement;
    declare diceOneTarget: HTMLSpanElement;
    declare diceTwoTarget: HTMLSpanElement;
    declare giveUpTarget: HTMLDivElement;

    declare enabledValue: boolean;
    declare diceOneValue: number;
    declare diceTwoValue: number;
    declare gameCodeValue: number;
    declare playerIdValue: number;
    declare positionValue: number;
    declare paschCount: number;
    declare diceDisabled: boolean;
    declare tradeDisabled: boolean;
    declare endTurnDisabled: boolean;
    declare giveUpDisabled: boolean;

    connect() {
      this.element.addEventListener("turn", (e: CustomEvent) => this.handleEvent(e.detail));

      this.paschCount = 0;
      this.diceOneValue = 0;
      this.diceTwoValue = 0;
      this.diceDisabled = false;
      this.tradeDisabled = false;
      this.endTurnDisabled = false;
      this.giveUpDisabled = false;

      this.diceOneTarget.innerText = this.diceOneValue.toString();
      this.diceTwoTarget.innerText = this.diceTwoValue.toString();

      if (this.enabledValue) {
        this.disableTrade();
        this.disableEndTurn();
      } else {
        this.disable();
      }
    }

    roll() {
      if (this.diceDisabled) {
        return;
      }

      this.disableDice();
      this.diceOneValue = Math.floor(Math.random() * 6) + 1;
      this.diceTwoValue = Math.floor(Math.random() * 6) + 1;

      this.diceOneTarget.innerText = this.diceOneValue.toString();
      this.diceTwoTarget.innerText = this.diceTwoValue.toString();

      this.positionValue += this.diceOneValue + this.diceTwoValue;

      if (this.diceOneValue === this.diceTwoValue) {
        this.paschCount++;
        if (this.paschCount === 3) {
          this.positionValue = 30;
          this.paschCount = 0;
        }
      }

      this.fetchTurn()
        .then(response => response.json())
        .then(data => {
          this.positionValue = data.position;

          if (this.paschCount === 0 || data.disable) {
            this.enableTrade();
            this.enableEndTurn();
          } else {
            this.enableDice();
            this.disableTrade();
            this.disableEndTurn();
          }
        });
    }

    endTurn() {
      if (this.endTurnDisabled) {
        return;
      }

      this.disable();
      this.fetchEndTurn()
        .then(response => response.json())
        .then(data => {
          if (data.success !== true) {
            alert(data.message);
          }
        });
    }

    giveUp() {
      if (this.giveUpDisabled) {
        return;
      }

      if (!confirm('Bist du dir sicher, dass du aufgeben möchtest?')) {
        return;
      }

      this.disable();
      fetch(window.location.origin + '/game/' + this.gameCodeValue + '/player/' + this.playerIdValue + '/bankrupt', {
        method: 'GET',
      })
        .then(response => response.json())
        .then(data => {
          if (data.success !== true) {
            alert(data.message);
          }

          this.disableGiveUp();
        });
    }

    handleEvent(data: { event: string; position: number; }) {
      if (data.event === 'turn') {
        this.positionValue = data.position;
        this.enableDice();
        this.disableTrade();
        this.disableEndTurn();
      } else if (data.event === 'end-turn') {
        this.paschCount = 0;
        this.disable();
      } else if (data.event === 'turn-rolled') {
        this.positionValue = data.position;
        this.disableDice();
        this.enableTrade();
        this.enableEndTurn();
      }
    }

    getTurnURL() {
      let pasch = 'false';
      if (this.paschCount > 0) {
        pasch = 'true';
      }

      return window.location.origin + '/game/' + this.gameCodeValue + '/turn/' + this.playerIdValue + '/' + this.positionValue + '/' + pasch;
    }

    getEndTurnURL() {
      return window.location.origin + '/game/' + this.gameCodeValue + '/turn/' + this.playerIdValue + '/end';
    }

    fetchTurn() {
      return fetch(this.getTurnURL(), {
        method: 'GET',
      })
    }

    fetchEndTurn() {
      return fetch(this.getEndTurnURL(), {
        method: 'GET',
      })
    }

    disable() {
      this.disableDice();
      this.disableTrade();
      this.disableEndTurn();
    }

    enable() {
      this.enableDice();
      this.enableTrade();
      this.enableEndTurn();
    }

    enableTrade() {
      this.tradeDisabled = false;
      this.tradeTarget.removeAttribute('disabled');
    }

    disableTrade() {
      this.tradeDisabled = true;
      this.tradeTarget.setAttribute('disabled', 'disabled');
    }

    enableDice() {
      this.diceDisabled = false;
      this.diceTarget.removeAttribute('disabled');
    }

    disableDice() {
      this.diceDisabled = true;
      this.diceTarget.setAttribute('disabled', 'disabled');
    }

    enableEndTurn() {
      this.endTurnDisabled = false;
      this.endTurnTarget.removeAttribute('disabled');
    }

    disableEndTurn() {
      this.endTurnDisabled = true;
      this.endTurnTarget.setAttribute('disabled', 'disabled');
    }

    enableGiveUp() {
      this.giveUpDisabled = false;
      this.giveUpTarget.removeAttribute('disabled');
    }

    disableGiveUp() {
      this.giveUpDisabled = true;
      this.giveUpTarget.setAttribute('disabled', 'disabled');
    }
}
