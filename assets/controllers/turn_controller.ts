import { Controller } from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['dice', 'trade', 'endTurn', 'diceOne', 'diceTwo']
    static values = {
      enabled: Boolean,
      diceOne: Number,
      diceTwo: Number,
      gameCode: Number,
      playerId: Number,
      position: Number,
    }

    declare paschCount: number;
    declare diceTarget: HTMLDivElement;
    declare tradeTarget: HTMLDivElement;
    declare endTurnTarget: HTMLDivElement;
    declare diceOneTarget: HTMLSpanElement;
    declare diceTwoTarget: HTMLSpanElement;

    declare enabledValue: boolean;
    declare diceOneValue: number;
    declare diceTwoValue: number;
    declare gameCodeValue: number;
    declare playerIdValue: number;
    declare positionValue: number;

    connect() {
      this.element.addEventListener("turn", (e: CustomEvent) => this.handleEvent(e.detail));

      this.paschCount = 0;
      this.diceOneValue = 0;
      this.diceTwoValue = 0;

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

          if (this.paschCount === 0) {
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
      this.disable();
      this.fetchEndTurn()
        .then(response => response.json())
        .then(data => {
          if (data.success !== true) {
            alert(data.message);
          }
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
      this.tradeTarget.removeAttribute('disabled');
    }

    disableTrade() {
      this.tradeTarget.setAttribute('disabled', 'disabled');
    }

    enableDice() {
      this.diceTarget.removeAttribute('disabled');
    }

    disableDice() {
      this.diceTarget.setAttribute('disabled', 'disabled');
    }

    enableEndTurn() {
      this.endTurnTarget.removeAttribute('disabled');
    }

    disableEndTurn() {
      this.endTurnTarget.setAttribute('disabled', 'disabled');
    }
}
