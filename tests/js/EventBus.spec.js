/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
import EventBus from '@js/lib/event-bus';

describe('EventBus', () => {
  let calls;
  const record = (name) => (...args) => calls.push([name, ...args]);

  beforeEach(() => {
    calls = [];
    EventBus.$off(['testA', 'testB']);
  });

  it('should be exposed as window.WishlistEventBus', () => {
    expect(window.WishlistEventBus).toBe(EventBus);
  });

  it('should pass the emitted arguments to the listeners', () => {
    EventBus.$on('testA', record('first'));
    EventBus.$on('testA', record('second'));
    EventBus.$emit('testA', 1, 2);

    expect(calls).toEqual([['first', 1, 2], ['second', 1, 2]]);
  });

  it('should chain its methods', () => {
    EventBus.$on('testA', record('a')).$on('testB', record('b')).$emit('testA').$emit('testB');

    expect(calls).toEqual([['a'], ['b']]);
  });

  it('should accept an array of events', () => {
    const listener = record('both');

    EventBus.$on(['testA', 'testB'], listener).$emit('testA').$emit('testB');
    EventBus.$off(['testA', 'testB'], listener).$emit('testA').$emit('testB');

    expect(calls).toEqual([['both'], ['both']]);
  });

  it('should call a $once listener a single time, unless removed first', () => {
    const removed = record('removed');

    EventBus.$once('testA', record('once')).$once('testA', removed);
    EventBus.$off('testA', removed);
    EventBus.$emit('testA', 'x').$emit('testA', 'y');

    expect(calls).toEqual([['once', 'x']]);
  });

  it('should remove every listener of an event when $off has no callback', () => {
    EventBus.$on('testA', record('a')).$on('testB', record('b'));
    EventBus.$off('testA').$emit('testA').$emit('testB');

    expect(calls).toEqual([['b']]);
  });

  it('should keep calling the listeners when one of them throws', () => {
    const {error} = console;
    const errors = [];
    console.error = (...args) => errors.push(args);

    try {
      EventBus.$on('testA', () => {
        throw new Error('Broken listener');
      }).$on('testA', record('next'));

      expect(() => EventBus.$emit('testA')).not.toThrow();
    } finally {
      console.error = error;
    }

    expect(calls).toEqual([['next']]);
    expect(errors).toHaveLength(1);
    expect(errors[0][1].message).toBe('Broken listener');
  });
});
