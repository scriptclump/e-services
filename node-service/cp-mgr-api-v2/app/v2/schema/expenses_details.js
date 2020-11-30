/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('expenses_details', {
    exp_det_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
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
    exp_det_date: {
      type: DataTypes.DATE,
      allowNull: false
    },
    exp_type: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    exp_det_description: {
      type: DataTypes.STRING(255),
      allowNull: false,
      defaultValue: '0'
    },
    exp_det_proof: {
      type: DataTypes.STRING(455),
      allowNull: false,
      defaultValue: '0'
    },
    exp_det_type: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    appr_det_status: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    reff_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true
    }
  }, {
    tableName: 'expenses_details'
  });
};
