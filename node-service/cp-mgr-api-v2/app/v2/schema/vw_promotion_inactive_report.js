/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_promotion_inactive_report', {
    prmt_det_id: {
      type: DataTypes.INTEGER(10).UNSIGNED,
      allowNull: false,
      defaultValue: '0'
    },
    prmt_tmpl_Id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    prmt_det_status: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    prmt_det_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    prmt_tmpl_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true
    },
    created_at_grid: {
      type: DataTypes.STRING(10),
      allowNull: true
    },
    end_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    end_date_grid: {
      type: DataTypes.STRING(10),
      allowNull: true
    },
    start_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    start_date_grid: {
      type: DataTypes.STRING(10),
      allowNull: true
    },
    offer_on: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    prmt_states: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    prmt_offer_type: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    prmt_offer_value: {
      type: DataTypes.STRING(54),
      allowNull: true
    },
    applied_ids: {
      type: DataTypes.STRING(250),
      allowNull: true
    },
    PrmtStatus: {
      type: DataTypes.STRING(8),
      allowNull: false,
      defaultValue: ''
    },
    ProductInformation: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    state_names: {
      type: DataTypes.TEXT,
      allowNull: true
    }
  }, {
    tableName: 'vw_promotion_inactive_report'
  });
};
