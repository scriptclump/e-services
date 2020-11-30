/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('expenses_history_details', {
    exp_history_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    exp_det_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    exp_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    exp_det_actual_amount: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00'
    },
    exp_det_approved_amount: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00'
    },
    exp_history_date: {
      type: DataTypes.DATE,
      allowNull: false
    },
    user_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    }
  }, {
    tableName: 'expenses_history_details'
  });
};
